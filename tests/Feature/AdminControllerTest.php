<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetUpRoles;
use Tests\SetUpTicketSystem;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase, SetUpRoles, SetUpTicketSystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
        $this->setUpTicketSystem();
    }

    public function test_index_displays_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Administrateur');

        $response = $this->actingAs($user)->get(route('admin.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('teamsProps')
            ->has('coordinateursProps')
            ->has('stats')
        );
    }

    public function test_index_prevents_unauthorized_access(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Conseiller');

        $response = $this->actingAs($user)->get(route('admin.index'));

        // Le middleware redirige vers planning au lieu de retourner 403
        $response->assertRedirect(route('planning'));
    }
}

