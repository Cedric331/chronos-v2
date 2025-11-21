<?php

namespace Tests\Feature;

use App\Models\Rotation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetUpRoles;
use Tests\TestCase;

class RotationControllerTest extends TestCase
{
    use RefreshDatabase, SetUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_store_creates_rotation(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $response = $this->actingAs($user)->post(route('team.post.rotation', $team), [
            'name' => 'Test Rotation',
            'details' => [],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('rotations', [
            'team_id' => $team->id,
            'name' => 'Test Rotation',
        ]);
    }

    public function test_store_requires_coordinateur_role(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->post(route('team.post.rotation', $team), [
            'name' => 'Test Rotation',
        ]);

        $response->assertStatus(403);
    }

    public function test_update_modifies_rotation(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $rotation = Rotation::factory()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user)->patch(route('team.patch.rotation', $rotation), [
            'name' => 'Updated Rotation',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('rotations', [
            'id' => $rotation->id,
            'name' => 'Updated Rotation',
        ]);
    }

    public function test_destroy_deletes_rotation(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $rotation = Rotation::factory()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user)->delete(route('team.delete.rotation', $rotation));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('rotations', [
            'id' => $rotation->id,
        ]);
    }
}

