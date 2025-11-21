<?php

namespace App\Services;

use App\Enums\PlanningType;
use App\Models\Planning;
use App\Models\User;
use App\Repositories\CalendarRepository;
use App\Repositories\PlanningRepository;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlanningService
{
    public function __construct(
        private PlanningRepository $planningRepository,
        private CalendarRepository $calendarRepository
    ) {}

    /**
     * Génère un planning pour un utilisateur sur une période donnée
     */
    public function generatePlanning(
        array $rotations,
        int $userId,
        Carbon $dateStart,
        Carbon $dateEnd,
        bool $typeFix = false
    ): int {
        $countDayGenerate = 0;
        $currentDate = $dateStart->copy();

        while ($currentDate->lte($dateEnd)) {
            foreach ($rotations as $rotation) {
                foreach ($rotation['details'] as $detail) {
                    $this->createPlanning(
                        $detail,
                        $rotation,
                        $userId,
                        $currentDate,
                        $typeFix
                    );
                    $currentDate->addDay();
                    $countDayGenerate++;

                    if ($currentDate->gt($dateEnd)) {
                        return $countDayGenerate;
                    }
                }
            }
        }

        return $countDayGenerate;
    }

    /**
     * Crée ou met à jour un planning pour une date donnée
     */
    private function createPlanning(
        array $detail,
        array $rotation,
        int $userId,
        Carbon $date,
        bool $typeFix
    ): void {
        $calendar = $this->calendarRepository->findByDate($date);

        if (! $calendar) {
            return;
        }

        $existingPlanning = $this->planningRepository
            ->findByCalendarAndUser($calendar->id, $userId);

        if ($existingPlanning && ! $typeFix) {
            try {
                $planningType = PlanningType::from($existingPlanning->type_day);
                if ($planningType->isFixed()) {
                    return;
                }
            } catch (\ValueError $e) {
                // Si le type n'existe pas dans l'Enum, utiliser la vérification par défaut
                if (in_array($existingPlanning->type_day, config('teams.type_days_fix'))) {
                    return;
                }
            }
        }

        $this->planningRepository->updateOrCreate([
            'id' => $existingPlanning?->id,
        ], [
            'type_day' => $detail['is_off'] ? PlanningType::REST->value : PlanningType::PLANNED->value,
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

    /**
     * Calcule les heures de travail pour une journée
     */
    public function calculateWorkHours(array|object $workDay): ?string
    {
        $isOff = is_array($workDay) ? ($workDay['is_off'] ?? false) : ($workDay->is_off ?? false);
        $debutJournee = is_array($workDay) ? ($workDay['debut_journee'] ?? null) : ($workDay->debut_journee ?? null);

        if ($isOff || ! $debutJournee) {
            return null;
        }

        $format = 'H:i';
        $startDayTime = is_array($workDay)
            ? str_replace('h', ':', $workDay['debut_journee'])
            : str_replace('h', ':', $workDay->debut_journee);
        $endDayTime = is_array($workDay)
            ? str_replace('h', ':', $workDay['fin_journee'])
            : str_replace('h', ':', $workDay->fin_journee);

        $startDay = DateTime::createFromFormat($format, $startDayTime);
        $endDay = DateTime::createFromFormat($format, $endDayTime);

        if (! $startDay || ! $endDay) {
            Log::warning('Erreur de format de date dans calculateWorkHours', [
                'startDayTime' => $startDayTime,
                'endDayTime' => $endDayTime,
            ]);

            return null;
        }

        $totalWorkHours = $endDay->diff($startDay)->h + ($endDay->diff($startDay)->i / 60);

        $debutPause = is_array($workDay) ? ($workDay['debut_pause'] ?? null) : ($workDay->debut_pause ?? null);
        $finPause = is_array($workDay) ? ($workDay['fin_pause'] ?? null) : ($workDay->fin_pause ?? null);

        if ($debutPause && $finPause) {
            $startBreakTime = is_array($workDay)
                ? str_replace('h', ':', $debutPause)
                : str_replace('h', ':', $debutPause);
            $endBreakTime = is_array($workDay)
                ? str_replace('h', ':', $finPause)
                : str_replace('h', ':', $finPause);

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

    /**
     * Vérifie si un type de jour nécessite des heures
     */
    public function checkTypeDay(string $typeDay): bool
    {
        try {
            $planningType = PlanningType::from($typeDay);

            return $planningType->requiresHours();
        } catch (\ValueError $e) {
            // Si le type n'existe pas dans l'Enum, utiliser la vérification par défaut
            return in_array($typeDay, ['Formation', 'Planifié', 'CP Matin', 'CP Après-midi']);
        }
    }

    /**
     * Calcule les heures hebdomadaires pour un calendrier
     */
    public function calculateWeeklyHours(Collection|EloquentCollection|array $calendar): array
    {
        $weeklyHours = [];

        // Convertir en collection si c'est un tableau
        if (is_array($calendar)) {
            $calendar = collect($calendar);
        }

        foreach ($calendar as $index => $day) {
            // Cas 1: Structure Calendar avec plannings (structure normale)
            if (isset($day->date)) {
                $date = $day->date;
                $plannings = $day->plannings ?? collect();

                // Convertir en collection si nécessaire
                if (is_array($plannings)) {
                    $plannings = collect($plannings);
                }

                $weekNumber = Carbon::parse($date)->isoFormat('W').Carbon::parse($date)->isoFormat('GGGG');

                // Assigner number_week directement à l'objet Calendar pour le frontend
                $day->number_week = $weekNumber;

                $firstPlanning = $plannings->first();
                if ($firstPlanning) {
                    $hours = is_array($firstPlanning)
                        ? ($firstPlanning['hours'] ?? null)
                        : ($firstPlanning->hours ?? null);

                    if ($hours !== null) {
                        $hoursArray = explode('h', $hours);
                        $decimalHours = intval($hoursArray[0]) + (isset($hoursArray[1]) ? intval($hoursArray[1]) / 60 : 0);

                        if (array_key_exists($weekNumber, $weeklyHours)) {
                            $weeklyHours[$weekNumber] += $decimalHours;
                        } else {
                            $weeklyHours[$weekNumber] = $decimalHours;
                        }
                    }
                }
            }
            // Cas 2: Structure User avec plannings (structure pour getPlanningTeam)
            elseif (isset($dayObj->plannings)) {
                $plannings = $dayObj->plannings;

                // Convertir en collection si nécessaire
                if (is_array($plannings)) {
                    $plannings = collect($plannings);
                }

                // Pour chaque planning, récupérer la date du calendrier
                foreach ($plannings as $planning) {
                    $planningObj = is_array($planning) ? (object) $planning : $planning;

                    // Récupérer la date depuis le calendrier associé
                    $calendarObj = null;
                    if (isset($planningObj->calendar)) {
                        $calendarObj = is_array($planningObj->calendar)
                            ? (object) $planningObj->calendar
                            : $planningObj->calendar;
                    }

                    if ($calendarObj && isset($calendarObj->date)) {
                        $date = $calendarObj->date;
                        $weekNumber = Carbon::parse($date)->isoFormat('W').Carbon::parse($date)->isoFormat('GGGG');

                        $hours = is_array($planning)
                            ? ($planning['hours'] ?? null)
                            : ($planningObj->hours ?? null);

                        if ($hours !== null) {
                            $hoursArray = explode('h', $hours);
                            $decimalHours = intval($hoursArray[0]) + (isset($hoursArray[1]) ? intval($hoursArray[1]) / 60 : 0);

                            if (array_key_exists($weekNumber, $weeklyHours)) {
                                $weeklyHours[$weekNumber] += $decimalHours;
                            } else {
                                $weeklyHours[$weekNumber] = $decimalHours;
                            }
                        }
                    }
                }
            }
        }

        foreach ($weeklyHours as $weekNumber => $decimalHours) {
            $weeklyHours[$weekNumber] = sprintf('%02d', intval($decimalHours)).'h'.sprintf('%02d', ($decimalHours - intval($decimalHours)) * 60);
        }

        return $weeklyHours;
    }
}
