<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\CalendarRepository;
use App\Repositories\PlanningRepository;
use Carbon\Carbon;

class CalendarService
{
    public function __construct(
        private CalendarRepository $calendarRepository,
        private PlanningRepository $planningRepository,
        private PlanningService $planningService
    ) {}

    /**
     * Récupère le planning d'un utilisateur à partir du lundi de la semaine actuelle
     */
    public function getPlanningForUser(User $user, ?User $selectedUser = null): array
    {
        $targetUser = $selectedUser ?? $user;
        $monday = Carbon::now()->startOfWeek();

        $calendar = $this->calendarRepository->getCalendarsWithPlanningsForUser(
            $targetUser->id,
            $user->team_id,
            $monday
        );

        // Assigner number_week à chaque jour du calendrier
        foreach ($calendar as $day) {
            if (isset($day->date)) {
                $day->number_week = Carbon::parse($day->date)->isoFormat('W').Carbon::parse($day->date)->isoFormat('GGGG');
            }
        }

        $weeklyHours = $this->planningService->calculateWeeklyHours($calendar);

        return [
            'calendar' => $calendar,
            'weeklyHours' => $weeklyHours,
        ];
    }

    /**
     * Récupère le planning personnalisé pour un utilisateur
     */
    public function getPlanningCustom(User $user, bool $getAllPlanning = false): array
    {
        $monday = Carbon::now()->startOfWeek();

        $startDate = $getAllPlanning ? null : $monday;

        $calendar = $this->calendarRepository->getCalendarsWithPlanningsForUser(
            $user->id,
            $user->team_id,
            $startDate
        );

        // Assigner number_week à chaque jour du calendrier
        foreach ($calendar as $day) {
            if (isset($day->date)) {
                $day->number_week = Carbon::parse($day->date)->isoFormat('W').Carbon::parse($day->date)->isoFormat('GGGG');
            }
        }

        $weeklyHours = $this->planningService->calculateWeeklyHours($calendar);

        return [
            'calendar' => $calendar,
            'weeklyHours' => $weeklyHours,
        ];
    }

    /**
     * Récupère le planning d'une équipe pour une semaine
     */
    public function getPlanningTeam(User $user, int $dayId, bool $mobile = false): array
    {
        $day = $this->calendarRepository->find($dayId);
        if (! $day) {
            return ['calendar' => collect([]), 'weeklyHours' => []];
        }

        $date = Carbon::parse($day->date);
        $startOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $date->copy()->endOfWeek(Carbon::SUNDAY);

        if ($mobile) {
            $calendar = $this->calendarRepository->getCalendarsForDateRange(
                $date,
                $date,
                [
                    'plannings' => function ($query) use ($user) {
                        $query->where('team_id', $user->team_id)
                            ->with(['eventPlannings', 'rotation']);
                    },
                    'plannings.user',
                ]
            );
        } else {
            $calendar = User::with(['plannings' => function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereHas('calendar', function ($q) use ($startOfWeek, $endOfWeek) {
                    $q->where('date', '>=', $startOfWeek)
                        ->where('date', '<=', $endOfWeek);
                })->with(['calendar', 'eventPlannings', 'rotation']);
            }])
                ->where('team_id', $user->team_id)
                ->whereHas('plannings.calendar', function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->where('date', '>=', $startOfWeek)
                        ->where('date', '<=', $endOfWeek);
                })
                ->get();
        }

        $weeklyHours = $this->planningService->calculateWeeklyHours($calendar);

        return [
            'calendar' => $calendar,
            'weeklyHours' => $weeklyHours,
        ];
    }
}
