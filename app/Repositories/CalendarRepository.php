<?php

namespace App\Repositories;

use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class CalendarRepository
{
    public function findByDate(Carbon $date): ?Calendar
    {
        return Calendar::whereDate('date', $date)->first();
    }

    public function findOrCreateByDate(Carbon $date): Calendar
    {
        return Calendar::firstOrCreate(
            ['date' => $date->format('Y-m-d')],
            ['date' => $date->format('Y-m-d')]
        );
    }

    public function getCalendarsForDateRange(
        Carbon $startDate,
        Carbon $endDate,
        array $with = []
    ): Collection {
        $query = Calendar::where('date', '>=', $startDate)
            ->where('date', '<=', $endDate);

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->get();
    }

    public function find(int $id): ?Calendar
    {
        return Calendar::find($id);
    }

    public function getCalendarsWithPlanningsForUser(
        int $userId,
        int $teamId,
        ?Carbon $startDate = null
    ): Collection {
        $query = Calendar::whereHas('plannings', function ($q) use ($userId, $teamId) {
            $q->where('user_id', $userId)
                ->where('team_id', $teamId);
        })
            ->with(['plannings' => function ($q) use ($userId) {
                $q->with(['eventPlannings', 'rotation'])->where('user_id', $userId);
            }]);

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        return $query->get();
    }
}

