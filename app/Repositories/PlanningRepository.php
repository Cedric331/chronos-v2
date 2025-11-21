<?php

namespace App\Repositories;

use App\Models\Planning;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PlanningRepository
{
    public function findByCalendarAndUser(int $calendarId, int $userId): ?Planning
    {
        return Planning::where('calendar_id', $calendarId)
            ->where('user_id', $userId)
            ->first();
    }

    public function getPlanningsForUser(
        int $userId,
        int $teamId,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): Collection {
        $query = Planning::where('user_id', $userId)
            ->where('team_id', $teamId)
            ->with(['calendar', 'eventPlannings']);

        if ($startDate) {
            $query->whereHas('calendar', function ($q) use ($startDate, $endDate) {
                $q->where('date', '>=', $startDate);
                if ($endDate) {
                    $q->where('date', '<=', $endDate);
                }
            });
        }

        return $query->get();
    }

    public function getTeamPlanningsForWeek(
        int $teamId,
        Carbon $startOfWeek,
        Carbon $endOfWeek
    ): Collection {
        $cacheKey = "team_plannings_{$teamId}_{$startOfWeek->format('Y-m-d')}";

        return Cache::remember($cacheKey, 3600, function () use ($teamId, $startOfWeek, $endOfWeek) {
            return Planning::where('team_id', $teamId)
                ->whereHas('calendar', function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->where('date', '>=', $startOfWeek)
                        ->where('date', '<=', $endOfWeek);
                })
                ->with(['calendar', 'eventPlannings', 'user'])
                ->get();
        });
    }

    public function getPlanningsForDateRange(
        int $userId,
        Carbon $startDate,
        Carbon $endDate
    ): Collection {
        return Planning::where('user_id', $userId)
            ->whereHas('calendar', function ($query) use ($startDate, $endDate) {
                $query->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate);
            })
            ->with('calendar')
            ->get();
    }

    public function updateOrCreate(array $attributes, array $values): Planning
    {
        return Planning::updateOrCreate($attributes, $values);
    }

    public function find(int $id): ?Planning
    {
        return Planning::find($id);
    }
}
