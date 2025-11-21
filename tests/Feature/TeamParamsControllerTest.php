<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\TeamParams;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetUpRoles;
use Tests\TestCase;

class TeamParamsControllerTest extends TestCase
{
    use RefreshDatabase, SetUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_update_modifies_team_params(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        // Le TeamParams est créé automatiquement par l'observer TeamObserver
        $team->refresh();
        $teamParams = $team->params;

        $response = $this->actingAs($user)->patchJson(route('teamParams.update', $teamParams), [
            'send_coordinateur' => true,
            'share_link_planning' => false,
            'share_link' => true,
            'paid_leave' => true,
            'update_planning' => true,
            'module_alert' => false,
            'exchange_module' => true,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('team_params', [
            'id' => $teamParams->id,
            'send_coordinateur' => true,
            'share_link_planning' => false,
        ]);
    }

    public function test_update_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        // Le TeamParams est créé automatiquement par l'observer TeamObserver
        $team->refresh();
        $teamParams = $team->params;

        $response = $this->actingAs($user)->patchJson(route('teamParams.update', $teamParams), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'send_coordinateur',
            'share_link_planning',
            'share_link',
            'paid_leave',
            'update_planning',
            'module_alert',
            'exchange_module',
        ]);
    }

    public function test_update_requires_coordinateur_role(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        // Le TeamParams est créé automatiquement par l'observer TeamObserver
        $team->refresh();
        $teamParams = $team->params;

        $response = $this->actingAs($user)->patchJson(route('teamParams.update', $teamParams), [
            'send_coordinateur' => true,
            'share_link_planning' => false,
            'share_link' => true,
            'paid_leave' => true,
            'update_planning' => true,
            'module_alert' => false,
            'exchange_module' => true,
        ]);

        $response->assertStatus(403);
    }
}

