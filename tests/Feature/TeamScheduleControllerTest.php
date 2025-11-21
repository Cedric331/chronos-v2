<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\TeamSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetUpRoles;
use Tests\TestCase;

class TeamScheduleControllerTest extends TestCase
{
    use RefreshDatabase, SetUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_store_creates_team_schedule(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $response = $this->actingAs($user)->postJson('/api/team-schedules', [
            [
                'day' => 'monday',
                'time' => '9h - 18h',
                'value' => 10,
                'team_id' => $team->id,
            ],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('team_schedules', [
            'day' => 'monday',
            'team_id' => $team->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $response = $this->actingAs($user)->postJson('/api/team-schedules', [
            [],
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['day', 'time', 'team_id']);
    }

    public function test_store_requires_coordinateur_role(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->postJson('/api/team-schedules', [
            [
                'day' => 'monday',
                'time' => '9h - 18h',
                'team_id' => $team->id,
            ],
        ]);

        $response->assertStatus(403);
    }
}

