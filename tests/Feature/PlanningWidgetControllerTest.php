<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanningWidgetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_preferences_creates_user_preferences(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->postJson(route('planning.widgets.preferences'), [
            'widgets' => [
                [
                    'id' => 'stats',
                    'component_name' => 'StatsWidget',
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
        ]);
    }

    public function test_update_preferences_updates_existing_preferences(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $preference = UserPreference::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson(route('planning.widgets.preferences'), [
            'widgets' => [
                [
                    'id' => 'events',
                    'component_name' => 'EventsWidget',
                ],
            ],
        ]);

        $response->assertStatus(200);

        $preference->refresh();
        $this->assertNotNull($preference->planning_widgets);
    }
}

