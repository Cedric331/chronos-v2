<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\Planning;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanningControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_stats_returns_planning_statistics(): void
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

        $response = $this->actingAs($user)->postJson(route('planning.stats'), [
            'period' => 'week',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'totalDays',
            'workDays',
            'plannedDays',
            'restDays',
            'teleworkDays',
            'period',
        ]);
    }

    public function test_get_events_returns_upcoming_events(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $calendar = Calendar::factory()->create(['date' => now()->addDay()]);
        Planning::factory()->create([
            'user_id' => $user->id,
            'calendar_id' => $calendar->id,
            'team_id' => $team->id,
        ]);

        $response = $this->actingAs($user)->postJson(route('planning.events'), [
            'daysAhead' => 7,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'title', 'date', 'type'],
        ]);
    }

    public function test_get_team_presence_returns_team_presence_info(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $otherUser = User::factory()->create(['team_id' => $team->id, 'company_id' => $user->company_id]);

        $calendar = Calendar::factory()->create(['date' => now()]);
        Planning::factory()->create([
            'user_id' => $otherUser->id,
            'calendar_id' => $calendar->id,
            'team_id' => $team->id,
            'type_day' => 'Planifié',
        ]);

        $response = $this->actingAs($user)->postJson(route('planning.team-presence'), [
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'isPresent'],
        ]);
    }

    public function test_get_stats_validates_period_parameter(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->postJson(route('planning.stats'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['period']);
    }

    public function test_get_events_validates_days_ahead_parameter(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->postJson(route('planning.events'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['daysAhead']);
    }

    public function test_get_team_presence_validates_date_parameter(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->postJson(route('planning.team-presence'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['date']);
    }
}

