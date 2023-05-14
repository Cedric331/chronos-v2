<?php

namespace App\Observers;

use App\Models\Team;
use App\Models\TeamParams;

class TeamObserver
{
    /**
     * Handle the Team "created" event.
     */
    public function created(Team $team): void
    {
        $teamParams = TeamParams::create([
            'type_day' => json_encode(config('teams.type_days_default')),
            'update_planning' => false,
        ]);
        $team->team_params_id = $teamParams->id;
        $team->save();
    }

    /**
     * Handle the Team "updated" event.
     */
    public function updated(Team $team): void
    {
        //
    }

    /**
     * Handle the Team "deleted" event.
     */
    public function deleted(Team $team): void
    {
        //
    }

    /**
     * Handle the Team "restored" event.
     */
    public function restored(Team $team): void
    {
        //
    }

    /**
     * Handle the Team "force deleted" event.
     */
    public function forceDeleted(Team $team): void
    {
        //
    }
}
