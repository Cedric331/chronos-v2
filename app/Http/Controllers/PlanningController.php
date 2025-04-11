<?php

namespace App\Http\Controllers;

use App\Exports\PlanningsExport;
use App\Http\Requests\RequestGeneratePlanning;
use App\Models\Calendar;
use App\Models\ExchangeRequest;
use App\Models\Planning;
use App\Models\Rotation;
use App\Models\ShareLink;
use App\Models\Team;
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
    /**
     * Get planning statistics for the widget.
     */
    public function getStats(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'period' => 'required|string|in:week,month,year',
        ]);

        $user = Auth::user();
        $period = $request->period;

        // Define the date range based on the period
        $startDate = null;
        $endDate = Carbon::now();

        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
        }

        // Calculer le nombre total de jours dans la période
        $totalDaysInPeriod = $startDate->diffInDays($endDate) + 1;

        // Calculer le nombre de jours ouvrables dans la période (lundi-vendredi)
        $workingDaysInPeriod = 0;
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            // 6 = samedi, 7 = dimanche
            if ($currentDate->dayOfWeek !== 6 && $currentDate->dayOfWeek !== 7) {
                $workingDaysInPeriod++;
            }
            $currentDate->addDay();
        }

        // Get the user's plannings within the date range
        $plannings = Planning::where('user_id', $user->id)
            ->whereHas('calendar', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->with('calendar')
            ->get();

        // Log tous les types de jours pour débogage
        $uniqueTypeDays = $plannings->pluck('type_day')->unique()->values()->toArray();
        \Illuminate\Support\Facades\Log::info('Types de jours dans les plannings:', $uniqueTypeDays);

        // Calculate statistics
        $totalDays = $totalDaysInPeriod;
        $plannedDays = $plannings->where('type_day', 'Planifié')->count();
        $workDays = $workingDaysInPeriod; // Nombre de jours ouvrables dans la période
        $restDays = $plannings->where('type_day', 'Repos')->count();
        $teleworkDays = $plannings->where('telework', true)->count();

        // Ajouter les types supplémentaires
        $paidLeaveDays = $plannings->where('type_day', 'Congés payés')->count();
        $sickLeaveDays = $plannings->where('type_day', 'Arrêt maladie')->count();
        $morningPaidLeaveDays = $plannings->where('type_day', 'CP matin')->count();
        $afternoonPaidLeaveDays = $plannings->where('type_day', 'CP après-midi')->count();
        $trainingDays = $plannings->where('type_day', 'Formation')->count();
        $holidayDays = $plannings->where('type_day', 'Jour férié')->count();

        // Essayer d'autres variantes possibles pour les jours fériés
        if ($holidayDays === 0) {
            $holidayDays += $plannings->where('type_day', 'Férié')->count();
            $holidayDays += $plannings->where('type_day', 'Ferie')->count();
            $holidayDays += $plannings->where('type_day', 'Jour ferie')->count();
            $holidayDays += $plannings->where('type_day', 'Jours fériés')->count();
        }

        // Ajouter tous les types uniques pour débogage
        $typesStats = [];
        foreach ($uniqueTypeDays as $type) {
            $typesStats['type_' . str_replace(' ', '_', strtolower($type)) . '_count'] = $plannings->where('type_day', $type)->count();
        }

        return response()->json(array_merge([
            'totalDays' => $totalDays,
            'workDays' => $workDays,
            'plannedDays' => $plannedDays,
            'restDays' => $restDays,
            'teleworkDays' => $teleworkDays,
            'paidLeaveDays' => $paidLeaveDays,
            'sickLeaveDays' => $sickLeaveDays,
            'morningPaidLeaveDays' => $morningPaidLeaveDays,
            'afternoonPaidLeaveDays' => $afternoonPaidLeaveDays,
            'trainingDays' => $trainingDays,
            'holidayDays' => $holidayDays,
            'workingDaysInPeriod' => $workingDaysInPeriod,
            'period' => $period,
        ], $typesStats));
    }

    /**
     * Get upcoming events for the widget.
     */
    public function getEvents(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'daysAhead' => 'required|integer|min:1|max:30',
        ]);

        $user = Auth::user();
        $daysAhead = $request->daysAhead;

        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addDays($daysAhead)->endOfDay();

        // Get the user's plannings within the date range
        $plannings = Planning::where('user_id', $user->id)
            ->whereHas('calendar', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->with('calendar')
            ->get();

        // Format the events
        $events = [];
        foreach ($plannings as $planning) {
            $events[] = [
                'id' => $planning->id,
                'title' => $planning->type_day,
                'date' => $planning->calendar->date,
                'type' => $planning->telework ? 'Télétravail' : $planning->type_day,
                'hours' => $planning->hours,
                'start_time' => $planning->debut_journee,
                'end_time' => $planning->fin_journee,
            ];
        }

        return response()->json($events);
    }

    /**
     * Get team presence information for the widget.
     */
    public function getTeamPresence(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $user = Auth::user();
        $date = Carbon::parse($request->date);

        // Get all users in the team
        $teamUsers = User::where('team_id', $user->team_id)
            ->where('account_active', true)
            ->get();

        // Get the calendar for the specified date
        $calendar = Calendar::whereDate('date', $date)->first();

        if (!$calendar) {
            return response()->json([]);
        }

        // Get all plannings for the team on that date
        $plannings = Planning::where('team_id', $user->team_id)
            ->where('calendar_id', $calendar->id)
            ->get()
            ->keyBy('user_id');

        // Format the team presence data
        $teamPresence = [];
        foreach ($teamUsers as $teamUser) {
            $planning = $plannings->get($teamUser->id);
            $isPresent = $planning && $planning->type_day === 'Planifié';

            $teamPresence[] = [
                'id' => $teamUser->id,
                'name' => $teamUser->name,
                'avatar' => $teamUser->avatar,
                'isPresent' => $isPresent,
                'telework' => $planning ? $planning->telework : false,
                'hours' => $planning ? $planning->hours : null,
            ];
        }

        return response()->json($teamPresence);
    }
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


        $countDayGenerate = 0;
        while (!$dateStart->eq($dateEnd)) {
            foreach ($request->rotations as $rotation) {
                foreach ($rotation['details'] as $detail) {
                    $this->createPlanning($detail, $rotation, $request->user, $dateStart, $request->type_fix);
                    $dateStart->addDay();
                    $countDayGenerate++;

                    if ($dateStart->eq($dateEnd)) {
                        activity(Auth::user()->team->name)
                            ->event('Mise à jour')
                            ->performedOn(User::find($request->user))
                            ->log('Un planning a été généré pour ' . User::find($request->user)->name);

                        return response()->json($countDayGenerate);
                    }
                }
            }
        }

        return response()->json('Erreur', 422);
    }

    private function createPlanning($detail, $rotation, $userId, Carbon $date, $type_fix): void
    {
        $calendar = Calendar::whereDate('date', $date)->first();

        if ($calendar) {
            $process = true;
            $planning = Planning::where('calendar_id', $calendar->id)
                ->where('user_id', $userId)
                ->first();
            if ($planning && !$type_fix && in_array($planning->type_day, config('teams.type_days_fix'))) {
                $process = false;
            }

            if ($process) {
                Planning::updateOrCreate([
                    'id' => $planning ? $planning->id : null],
                    [
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
            }

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

            $user = User::find($planning->user_id);
        }

        if ($user && $planning) {
            activity(Auth::user()->team->name)
                ->event('Mise à jour')
                ->performedOn($user)
                ->withProperties($planning->getOriginal())
                ->log('Le planning de '.$user->name.' a été modifié');
        }

        $calendar = Calendar::with(['plannings' => function ($query) use ($planning) {
            $query->with('eventPlannings')->where('user_id', $planning->user_id);
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
            $currentDate = Carbon::parse($day)->isoFormat('dddd');

            foreach ($rotation->details as $detail) {
                if (strtolower($detail['day']) === $currentDate) {

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

                    activity(Auth::user()->team->name)
                        ->event('Mise à jour')
                        ->performedOn($planning)
                        ->withProperties($planning->getOriginal())
                        ->log('Les informations du planning ont été modifiées');
                }
            }
        }

        $calendar = Calendar::with(['plannings' => function ($query) use ($planning) {
            $query->with('eventPlannings')->where('user_id', $planning->user_id);
        }])->find($ids);

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

        activity(Auth::user()->team->name)
            ->event('Enregistrement')
            ->log('Un lien de partage a été généré');

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

            activity(Auth::user()->team->name)
                ->event('Suppression')
                ->log('Un lien de partage a été supprimé');
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

        if (!$shareLink) {
            abort(404, 'Lien de partage non valide ou expiré');
        }

        $user = User::find($shareLink->user_id);

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
            $calendar = Calendar::where('date', $date)
                ->whereHas('plannings', function ($query) use ($user) {
                    $query->where('team_id', $user->team_id);
                })
                ->with([
                    'plannings' => function ($query) use ($user) {
                        $query->where('team_id', $user->team_id)
                            ->with('eventPlannings');
                    },
                    'plannings.user',
                ])
                ->get();
        } else {
            $calendar = User::where('team_id', $user->team_id)
                ->whereHas('plannings.calendar', function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->where('date', '>=', $startOfWeek)
                        ->where('date', '<=', $endOfWeek);
                })
                ->with(['plannings' => function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->whereHas('calendar', function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->where('date', '>=', $startOfWeek)
                            ->where('date', '<=', $endOfWeek);
                    })->with(['calendar', 'eventPlannings']);
                }])
                ->get();
        }

        $weeklyHours = [];
        $coutner = 0;
        foreach ($calendar as $day) {
            foreach ($day->plannings as $planning) {
                $planning->number_week = Carbon::parse($day->date)->isoFormat('W').$coutner;
                $weekNumber = $planning->number_week;

                if (isset($planning->hours) && $planning->hours !== null) {
                    $hoursArray = explode('h', $planning->hours);
                    $decimalHours = intval($hoursArray[0]) + intval($hoursArray[1]) / 60;

                    if (array_key_exists($weekNumber, $weeklyHours)) {
                        $weeklyHours[$weekNumber] += $decimalHours;
                    } else {
                        $weeklyHours[$weekNumber] = $decimalHours;
                    }
                }
            }
            $coutner++;
        }

        foreach ($weeklyHours as $weekNumber => $decimalHours) {
            $weeklyHours[$weekNumber] = sprintf('%02d', intval($decimalHours)) . 'h' . sprintf('%02d', ($decimalHours - intval($decimalHours)) * 60);
        }

        return response()->json(['calendar' => $calendar, 'weeklyHours' => $weeklyHours]);
    }

    private function checkTypeDay($typeDay): bool
    {
        if ($typeDay === 'Formation' || $typeDay === 'Planifié' || $typeDay === 'CP Matin' || $typeDay === 'CP Après-midi') {
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

    public function exportPlanning(): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $user = Auth::user();
        return (new PlanningsExport)->user($user)->download('planning.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Récupère les plannings d'un utilisateur spécifique pour les 30 prochains jours
     * Utilisé par l'API pour le module d'échange de planning
     */
    public function getUserPlannings(User $user): \Illuminate\Http\JsonResponse
    {
        $currentUser = Auth::user();

        // Vérifier que l'utilisateur connecté et l'utilisateur demandé sont dans la même équipe
        if ($currentUser->team_id !== $user->team_id) {
            return response()->json(['error' => 'Vous n\'avez pas accès aux plannings de cet utilisateur'], 403);
        }

        // Récupérer les plannings de l'utilisateur pour les 30 prochains jours
        $plannings = Planning::with('calendar')
            ->where('user_id', $user->id)
            ->whereHas('calendar', function ($query) {
                $query->where('date', '>=', Carbon::now()->startOfDay())
                    ->where('date', '<=', Carbon::now()->addDays(30)->endOfDay());
            })
            ->get();

        return response()->json($plannings);
    }

    /**
     * Récupère les dates disponibles pour un échange de planning entre l'utilisateur connecté et un autre utilisateur
     * Utilisé par l'API pour le module d'échange de planning
     */
    public function getExchangeDates(User $user): \Illuminate\Http\JsonResponse
    {
        $currentUser = Auth::user();

        // Vérifier que l'utilisateur connecté et l'utilisateur demandé sont dans la même équipe
        if ($currentUser->team_id !== $user->team_id) {
            return response()->json(['error' => 'Vous n\'avez pas accès aux plannings de cet utilisateur'], 403);
        }

        // Récupérer les dates communes aux deux utilisateurs pour les 30 prochains jours
        $dates = Calendar::whereHas('plannings', function ($query) use ($currentUser) {
                $query->where('user_id', $currentUser->id);
            })
            ->whereHas('plannings', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('date', '>=', Carbon::now()->startOfDay())
            ->where('date', '<=', Carbon::now()->addDays(30)->endOfDay())
            ->with(['plannings' => function ($query) use ($currentUser, $user) {
                $query->whereIn('user_id', [$currentUser->id, $user->id]);
            }])
            ->get();

        // Vérifier s'il existe déjà des demandes d'échange en cours pour ces plannings
        $existingExchangeRequests = ExchangeRequest::where('status', ExchangeRequest::STATUS_PENDING)
            ->where(function ($query) use ($currentUser, $user) {
                $query->where('requester_id', $currentUser->id)
                    ->orWhere('requester_id', $user->id)
                    ->orWhere('requested_id', $currentUser->id)
                    ->orWhere('requested_id', $user->id);
            })
            ->get();

        $existingPlanningIds = [];
        foreach ($existingExchangeRequests as $request) {
            $existingPlanningIds[] = $request->requester_planning_id;
            $existingPlanningIds[] = $request->requested_planning_id;
        }

        // Formater les données pour le front-end
        $availableDates = [];
        foreach ($dates as $date) {
            $yourPlanning = null;
            $colleaguePlanning = null;

            foreach ($date->plannings as $planning) {
                if ($planning->user_id === $currentUser->id) {
                    $yourPlanning = $planning;
                } else if ($planning->user_id === $user->id) {
                    $colleaguePlanning = $planning;
                }
            }

            // Vérifier que les deux plannings existent et ne sont pas déjà dans une demande d'échange
            if ($yourPlanning && $colleaguePlanning &&
                !in_array($yourPlanning->id, $existingPlanningIds) &&
                !in_array($colleaguePlanning->id, $existingPlanningIds)) {
                $availableDates[] = [
                    'calendar' => $date,
                    'yourPlanning' => $yourPlanning,
                    'colleaguePlanning' => $colleaguePlanning
                ];
            }
        }

        return response()->json($availableDates);
    }
}
