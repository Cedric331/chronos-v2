<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestGeneratePlanning;
use App\Models\Calendar;
use App\Models\Planning;
use App\Models\Rotation;
use App\Models\ShareLink;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PlanningController extends Controller
{
    protected array $hours = [
        '08h00',
        '08h30',
        '09h00',
        '09h30',
        '10h00',
        '10h30',
        '11h00',
        '11h30',
        '12h00',
        '12h30',
        '13h00',
        '13h30',
        '14h00',
        '14h30',
        '15h00',
        '15h30',
        '16h00',
        '16h30',
        '17h00',
        '17h30',
        '18h00',
        '18h30',
        '19h00',
        '19h30',
        '20h00',
        '20h30',
        '21h00',
    ];

    public function __construct()
    {
        $this->middleware('isCoordinateur')->only('generate');
    }

    public function generate(RequestGeneratePlanning $request): \Illuminate\Http\JsonResponse
    {
        $dateStart = Carbon::parse($request->dateStart);
        $dateEnd = Carbon::parse($request->dateEnd)->addWeek();

        if ($dateStart->gt($dateEnd)) {
            return response()->json('Erreur dans la sélection des dates', 422);
        }

        // TODO Gérer les plannings déjà planifié (CP, RJF...)
        Planning::where('user_id', $request->user)->delete();

        $countDayGenerate = 0;
        while (! $dateStart->eq($dateEnd)) {
            foreach ($request->rotations as $rotation) {
                foreach ($rotation['details'] as $detail) {
                    $this->createPlanning($detail, $rotation, $request->user, $dateStart);
                    $dateStart->addDay();
                    $countDayGenerate++;

                    if ($dateStart->eq($dateEnd)) {
                        return response()->json($countDayGenerate);
                    }
                }
            }
        }

        return response()->json('Erreur', 422);
    }

    private function createPlanning($detail, $rotation, $userId, Carbon $date): void
    {
        $calendar = Calendar::whereDate('date', $date)->first();

        if ($calendar) {
            $planning = Planning::create([
                'type_day' => $detail['is_off'] ? 'Repos' : 'Planifié',
                'debut_journee' => $detail['debut_journee'],
                'debut_pause' => $detail['debut_pause'],
                'fin_pause' => $detail['fin_pause'],
                'fin_journee' => $detail['fin_journee'],
                'is_technician' => $detail['technicien'],
                'telework' => $detail['teletravail'],
                'hours' => $this->calculateWorkHours($detail),
                'calendar_id' => $calendar->id,
                'rotation_id' => $rotation['id'],
                'team_id' => Auth::user()->team_id,
                'user_id' => $userId,
            ]);

            response()->json($planning);
        }
    }

    public function update(Request $request): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        if (! Gate::check('can-update-planning')) {
            return response()->json(['message' => 'Action non autorisée'], 401);
        }

        if (! $request->ajax()) {
            return Inertia::render('Errors/404');
        }

        $ids = array_column($request->days, 'id');
        foreach ($request->days as $day) {
            $planning = Planning::find($day['plannings'][0]['id']);
            if ($this->checkTypeDay($request->type_day)) {

                $planning->update([
                    'type_day' => $request->type_day,
                    'debut_journee' => $request->debut_journee,
                    'debut_pause' => array_search($request->debut_pause, $this->hours) && array_search($request->fin_pause, $this->hours) ? $request->debut_pause : null,
                    'fin_pause' => array_search($request->debut_pause, $this->hours) && array_search($request->fin_pause, $this->hours) ? $request->fin_pause : null,
                    'fin_journee' => $request->fin_journee,
                    'is_technician' => $request->is_technician,
                    'telework' => $request->telework,
                    'hours' => $this->calculateWorkHours($request),
                ]);
            } else {
                $planning->update([
                    'type_day' => $request->type_day,
                    'debut_journee' => null,
                    'debut_pause' => null,
                    'fin_pause' => null,
                    'fin_journee' => null,
                    'is_technician' => false,
                    'telework' => false,
                    'hours' => null,
                ]);
            }
        }

        $calendar = Calendar::with(['plannings' => function ($query) use ($planning) {
            $query->where('user_id', $planning->user_id);
        }])->find($ids);

        return response()->json($calendar);
    }

    public function updateWithRotation(Request $request): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        if (! Gate::check('can-update-planning')) {
            return response()->json(['message' => 'Action non autorisée'], 401);
        }

        if (! $request->ajax()) {
            return Inertia::render('Errors/404');
        }

        $rotation = Rotation::find($request->rotation_id);
        $ids = array_column($request->days, 'id');
        foreach ($request->days as $day) {
            $planning = Planning::find($day['plannings'][0]['id']);
            $day = strtolower($planning->calendar->getDay());
            foreach ($rotation->details as $detail) {
                if (strtolower($detail['day']) === $day) {

                    $planning->update([
                        'debut_journee' => $detail->debut_journee,
                        'debut_pause' => $detail->debut_pause,
                        'fin_pause' => $detail->fin_pause,
                        'fin_journee' => $detail->fin_journee,
                        'telework' => $detail->teletravail,
                        'is_technician' => $detail->technicien,
                        'rotation_id' => $rotation->id,
                        'type_day' => $detail->is_off ? 'Repos' : 'Planifié',
                    ]);

                }
            }
        }

        $calendar = Calendar::with(['plannings' => function ($query) use ($planning) {
            $query->where('user_id', $planning->user_id);
        }])
            ->find($ids);

        return response()->json($calendar);
    }

    public function generateShareLink(Request $request): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        if (! $request->ajax()) {
            return Inertia::render('Errors/404');
        }

        $request->validate([
            'selected_time' => 'required|string',
        ]);

        $user = User::find(Auth::id());

        if ($user->getLinks()->count() >= 5) {
            return response()->json(['errors' => 'Vous avez atteint le nombre maximum de liens de partage. Veuillez en supprimer dans votre compte afin de pouvoir en générér'], 422);
        }

        if (! $user->hasPlanning) {
            return response()->json(['errors' => 'Vous n\'avez pas de planning. Vous devez avoir un planning afin de pouvoir générer un lien de partage'], 422);
        }

        $token = Str::random(24);
        $selectedTime = $request->input('selected_time');

        $validity = match ($selectedTime) {
            '1 semaine' => 7,
            '1 mois' => 30,
            '1 an' => 365,
            default => 1,
        };

        ShareLink::create([
            'token' => $token,
            'user_id' => $user->id,
            'expired_at' => now()->addDays($validity),
        ]);

        return response()->json(['link' => url("/planning/{$token}")]);
    }

    public function deleteShareLink(Request $request, ShareLink $link): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        if (! $request->ajax()) {
            return Inertia::render('Errors/404');
        }

        $user = User::find(Auth::id());
        if ($user->id === $link->user_id) {
            $link->delete();
        } else {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à supprimer ce lien'], 403);
        }

        return response()->json('Lien supprimé avec succès!');
    }

    public function getPlanningShare($token): \Inertia\Response
    {
        $shareLink = ShareLink::where('token', $token)
            ->where('expired_at', '>', now())
            ->first();

        if (! $shareLink) {
            abort(404, 'Lien de partage non valide ou expiré');
        }

        $user = User::find($shareLink->user_id);

        // Récupérer et afficher le planning de l'utilisateur
        $days = $this->getPlanning($user->id);

        $shareLink->update([
            'count_view' => $shareLink->count_view + 1,
        ]);

        return Inertia::render('Calendar/ShareCalendar', [
            'user' => $user->name,
            'daysProps' => $days,
            'isToday' => ucwords(Carbon::now()->isoFormat('dddd D MMMM')),
        ]);
    }

    private function getPlanning($id)
    {
        $user = User::find($id);

        $calendar = Calendar::whereHas('plannings', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['plannings' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->where('date', '>=', Carbon::now()->startOfWeek())
            ->get();

        foreach ($calendar as $day) {
            $day->number_week = Carbon::parse($day->date)->isoFormat('W');
        }

        return $calendar;
    }

    public function getPlanningTeam(Request $request): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        if (! $request->ajax()) {
            return Inertia::render('Errors/404');
        }

        $user = User::find(Auth::id());
        $day = Calendar::find($request->day_id);
        $date = Carbon::parse($day->date);

        $startOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $date->copy()->endOfWeek(Carbon::SUNDAY);

        if ($request->mobile) {
            $calendar = Calendar::whereHas('plannings', function ($query) use ($user) {
                $query->where('team_id', $user->team_id);
            })
                ->with(['plannings' => function ($query) use ($user) {
                    $query->where('team_id', $user->team_id);
                }, 'plannings.user'])
                ->where('date', '=', $date)
                ->get();
        } else {
            $calendar = User::where('team_id', $user->team_id)
                ->whereHas('plannings', function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->whereHas('calendar', function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->where('date', '>=', $startOfWeek)
                            ->where('date', '<=', $endOfWeek);
                    });
                })
                ->with(['plannings' => function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->whereHas('calendar', function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->where('date', '>=', $startOfWeek)
                            ->where('date', '<=', $endOfWeek);
                    });
                }, 'plannings.calendar'])
                ->get();
        }

        return response()->json($calendar);
    }

    private function checkTypeDay($typeDay): bool
    {
        if ($typeDay === 'Formation' || $typeDay === 'Planifié') {
            return true;
        }

        return false;
    }

    private function calculateWorkHours($workDay): ?string
    {
        $format = 'H:i';
        if ($workDay['is_off'] || ! $workDay['debut_journee']) {
            return null;
        }

        $startDayTime = is_array($workDay) ? str_replace('h', ':', $workDay['debut_journee']) : str_replace('h', ':', $workDay->debut_journee);
        $endDayTime = is_array($workDay) ? str_replace('h', ':', $workDay['fin_journee']) : str_replace('h', ':', $workDay->fin_journee);

        $startDay = DateTime::createFromFormat($format, $startDayTime);
        $endDay = DateTime::createFromFormat($format, $endDayTime);

        $totalWorkHours = $endDay->diff($startDay)->h + ($endDay->diff($startDay)->i / 60);

        if (isset($workDay['debut_pause']) && isset($workDay['fin_pause'])) {
            $startBreakTime = is_array($workDay) ? str_replace('h', ':', $workDay['debut_pause']) : str_replace('h', ':', $workDay->debut_pause);
            $endBreakTime = is_array($workDay) ? str_replace('h', ':', $workDay['fin_pause']) : str_replace('h', ':', $workDay->fin_pause);

            if ($startBreakTime !== null && $endBreakTime !== null) {
                $startBreak = DateTime::createFromFormat($format, $startBreakTime);
                $endBreak = DateTime::createFromFormat($format, $endBreakTime);

                if ($startBreak && $endBreak) {
                    $breakHours = $endBreak->diff($startBreak)->h + ($endBreak->diff($startBreak)->i / 60);
                    $totalWorkHours -= $breakHours;
                }
            }
        }

        $hours = intval($totalWorkHours);
        $minutes = ($totalWorkHours - $hours) * 60;

        return sprintf('%02d', $hours).'h'.sprintf('%02d', $minutes);
    }
}
