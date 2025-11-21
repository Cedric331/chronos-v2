<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\EventPlanning;
use App\Models\Planning;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetUpRoles;
use Tests\TestCase;

class EventPlanningControllerTest extends TestCase
{
    use RefreshDatabase, SetUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_store_creates_event_planning(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $calendar = Calendar::factory()->create();
        $planning = Planning::factory()->create([
            'user_id' => $user->id,
            'calendar_id' => $calendar->id,
            'team_id' => $team->id,
        ]);

        $response = $this->actingAs($user)->postJson(route('event.post'), [
            'title' => 'Test Event',
            'description' => 'Test Description',
            'days' => [
                [
                    'plannings' => [
                        [
                            'id' => $planning->id,
                            'calendar_id' => $calendar->id,
                            'team_id' => $team->id,
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([['id', 'title', 'description']]);

        $this->assertDatabaseHas('event_plannings', [
            'title' => 'Test Event',
            'planning_id' => $planning->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $response = $this->actingAs($user)->postJson(route('event.post'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'days']);
    }

    public function test_store_requires_coordinateur_role(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->postJson(route('event.post'), [
            'title' => 'Test Event',
        ]);

        $response->assertStatus(403);
    }

    public function test_delete_removes_event_planning(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $calendar = Calendar::factory()->create();
        $planning = Planning::factory()->create([
            'user_id' => $user->id,
            'calendar_id' => $calendar->id,
            'team_id' => $team->id,
        ]);

        $eventPlanning = EventPlanning::factory()->create([
            'planning_id' => $planning->id,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('event.delete'), [
            'id' => $eventPlanning->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('event_plannings', [
            'id' => $eventPlanning->id,
        ]);
    }
}

