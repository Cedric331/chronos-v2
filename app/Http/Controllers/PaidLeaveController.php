<?php

namespace App\Http\Controllers;

use App\Models\PaidLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaidLeaveController extends Controller
{
    public function view()
    {
        $user = Auth::user();
        $paidleaves = PaidLeave::with(['calendars', 'user'])
            ->where('team_id', $user->team_id)
            ->get();

        return Inertia::render('PaidLeave/PaidLeave', [
            'leavesProps' => $paidleaves
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
            ->where('team_id', $user->team_id)
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
            'team_id' => $user->team_id,
        ]);

        $paidLeave->calendars()->attach($calendars);

        return response()->json($paidLeave);
    }
}
