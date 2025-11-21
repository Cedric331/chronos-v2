<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetUpRoles;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
    use RefreshDatabase, SetUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_show_displays_team_information(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        // Assigner le rôle coordinateur à l'utilisateur
        $user->assignRole('Coordinateur');

        $response = $this->actingAs($user)->get(route('team.show', ['name' => $team->name]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('team'));
    }

    public function test_show_prevents_unauthorized_access(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $otherTeam = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->get(route('team.show', ['name' => $otherTeam->name]));

        $response->assertRedirect('/');
    }

    public function test_get_information_displays_team_information(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->get(route('information.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('usersProps'));
    }

    public function test_paginate_activities_returns_team_activities(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->getJson(route('team.activities', ['team_id' => $team->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'current_page',
            'per_page',
        ]);
    }
}

