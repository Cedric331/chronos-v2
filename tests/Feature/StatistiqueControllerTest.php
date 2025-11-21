<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\Planning;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatistiqueControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_statistics_returns_planning_statistics(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $calendar = Calendar::factory()->create(['date' => now()]);
        Planning::factory()->create([
            'user_id' => $user->id,
            'calendar_id' => $calendar->id,
            'team_id' => $team->id,
            'type_day' => 'Planifié',
        ]);

        $response = $this->actingAs($user)->getJson('/api/statistics', [
            'user_id' => $user->id,
            'start_date' => now()->subDays(7)->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['Planifié']);
    }
}

