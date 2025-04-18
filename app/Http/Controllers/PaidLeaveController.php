<?php

namespace App\Http\Controllers;

use App\Exports\PaidLeaveExport;
use App\Mail\NewRequestLeave;
use App\Models\Calendar;
use App\Models\PaidLeave;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class PaidLeaveController extends Controller
{
    public function view()
    {
        $user = Auth::user();
        $users = User::where('team_id', $user->team_id)
            ->withCount('daysPaidAccepted')
            ->role('Conseiller')
            ->get();

        $paidleaves = PaidLeave::with(['calendars', 'user'])
            ->where('team_id', $user->team_id)
            ->orderByRaw("FIELD(status, '" . PaidLeave::STATUS_PENDING . "') DESC")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $dates = Calendar::whereHas('paidleaves', function ($query) use ($user) {
            $query->where('team_id', $user->team_id);
        })->get()->pluck('date');

        $uniqueYears = $dates->map(function ($date) {
            $date = Carbon::parse($date);
            if ($date->month < 6) {
                $date->subYear();
            }
            return Carbon::parse($date)->year;
        })->unique()->values();

        $yearRanges = $uniqueYears->map(function ($year) {
            return [
                'value' => $year . ' - ' . ($year + 1),
                'option' => 'Juin ' . $year . ' - Mai ' . ($year + 1)
            ];
        });

        $date = Carbon::now();
        if ($date->month < 6) {
            $date->subYear();
        }
        $dateYear = Carbon::parse($date)->year;
        return Inertia::render('PaidLeave/PaidLeave', [
            'leavesProps' => $paidleaves,
            'yearsProps' => $yearRanges,
            'usersProps' => $users,
            'yearsRange' => 'Juin ' . $dateYear . ' - Mai ' . $dateYear + 1,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'days' => 'required|array',
            'type' => 'required|string',
            'comment' => 'nullable|string',
        ], [
            'days.required' => 'Vous devez sélectionner au moins un jour',
            'type.required' => 'Vous devez sélectionner un type de congés',
        ]);

        if (!in_array($request->type, config('teams.type_days_off'))) {
            return response()->json([
                'message' => 'Vous ne pouvez pas poser ce type de congés'
            ], 403);
        }

        $user = Auth::user();
        $team = Team::find($user->team_id);

        $calendars = collect($request->days)
            ->map(function ($day) {
                return data_get($day, 'plannings.0.calendar_id');
            })
            ->filter()
            ->unique();

        $paidLeave = PaidLeave::whereHas('calendars', function ($query) use ($calendars) {
            $query->whereIn('calendar_id', $calendars);
        })
            ->where('user_id', $user->id)
            ->where('team_id', $team->id)
            ->first();

        if ($paidLeave) {
            return response()->json([
                'message' => 'Vous avez déjà une demande pour des jours sur cette période.'
            ], 403);
        }

        $paidLeave = PaidLeave::create([
            'type' => $request->type,
            'comment' => $request->comment,
            'user_id' => $user->id,
            'team_id' => $team->id,
            'status' => PaidLeave::STATUS_PENDING
        ]);

        $paidLeave->calendars()->attach($calendars);

        // send mail
        if ($team->user_id !== null) {
            $userCoordinateur = User::find($team->user_id);
            $content = [
                'coordinateur_name' => $userCoordinateur->name,
                'name' => $user->name,
                'leave_type' => $paidLeave->type,
                'comment' => $paidLeave->comment,
                'dates' => $paidLeave->calendars->pluck('date_fr')->toArray(),
                'url' => route('paidleave.index')
            ];
            Mail::to($userCoordinateur->email)->queue(new NewRequestLeave($content));
        }

        return response()->json($paidLeave);
    }

    public function accepted(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $paidLeave = PaidLeave::find($request->id);

        if (!$paidLeave) {
            return response()->json([
                'message' => 'La demande n\'existe pas'
            ], 404);
        }

        $paidLeave->update([
            'status' => PaidLeave::STATUS_ACCEPTED
        ]);

        // TODO: Send mail to user

        return response()->json($paidLeave);
    }

    public function refused(Request $request)
    {
        if (!Auth::user()->isCoordinateur()) {
            return response()->json([
                'message' => 'Vous n\'avez pas les droits pour refuser une demande'
            ], 403);
        }

        $request->validate([
            'id' => 'required|integer',
        ]);

        $paidLeave = PaidLeave::find($request->id);

        if (!$paidLeave) {
            return response()->json([
                'message' => 'La demande n\'existe pas'
            ], 404);
        }

        $paidLeave->update([
            'status' => PaidLeave::STATUS_REFUSED
        ]);

        // TODO: Send mail to user

        return response()->json($paidLeave);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $paidLeave = PaidLeave::find($request->id);

        if (!$paidLeave) {
            return response()->json([
                'message' => 'La demande n\'existe pas'
            ], 404);
        }

        $paidLeave->calendars()->detach();
        $paidLeave->delete();

        return response()->json($paidLeave);
    }

    public function search (Request $request)
    {
        $years = explode('-', $request->year);

        $user = Auth::user();
        $paidleaves = PaidLeave::with(['calendars', 'user'])
            ->whereHas('user', function ($query) use ($request) {
                if ($request->user) {
                    $query->where('id', $request->user);
                }
            })
            ->whereHas('calendars', function ($query) use ($years) {
                if ($years[0] && $years[1]) {
                    $startDate = Carbon::createFromDate(trim($years[0]), 6, 1);
                    $endDate = Carbon::createFromDate(trim($years[1]), 5, 31);

                    $query->where(function ($subQuery) use ($startDate, $endDate) {
                        $subQuery->where('date', '>=', $startDate)
                            ->where('date', '<=', $endDate);
                    });
                }
            })
            ->where('team_id', $user->team_id)
            ->orderByRaw("FIELD(status, '" . PaidLeave::STATUS_PENDING . "') DESC")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($paidleaves);
    }

    public function statistics(Request $request)
    {
        $user = Auth::user();
        $years = [];

        if ($request->year) {
            $years = explode('-', $request->year);
        }

        $paidleaves = PaidLeave::where('team_id', $user->team_id)
            ->whereHas('user', function ($query) use ($request) {
                if ($request->user) {
                    $query->where('id', $request->user);
                }
            })
            ->whereHas('calendars', function ($query) use ($years) {
                if ($years) {
                    $startDate = Carbon::createFromDate(trim($years[0]), 6, 1);
                    $endDate = Carbon::createFromDate(trim($years[1]), 5, 31);

                    $query->where(function ($subQuery) use ($startDate, $endDate) {
                        $subQuery->where('date', '>=', $startDate)
                            ->where('date', '<=', $endDate);
                    });
                }
            })
            ->get();

        return response()->json($paidleaves);
    }

    public function export(Request $request)
    {
        $team = Team::find(Auth::user()->team_id);
        $years = [];

        if ($request->year) {
            $years = explode('-', $request->year);
        }

        return (new PaidLeaveExport)->team($team)->user($request->user)->years($years)->download('leavepaid.xlsx');
    }
}
