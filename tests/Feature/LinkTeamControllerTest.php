<?php

namespace Tests\Feature;

use App\Models\LinkTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetUpRoles;
use Tests\TestCase;

class LinkTeamControllerTest extends TestCase
{
    use RefreshDatabase, SetUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_store_creates_link_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $response = $this->actingAs($user)->postJson(route('link.store'), [
            'lien' => 'https://example.com',
            'description' => 'Test link',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('link_teams', [
            'link' => 'https://example.com',
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $response = $this->actingAs($user)->postJson(route('link.store'), []);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['lien']);
    }

    public function test_update_modifies_link_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $link = LinkTeam::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $response = $this->actingAs($user)->patchJson(route('link.patch', $link), [
            'lien' => 'https://updated.com',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('link_teams', [
            'id' => $link->id,
            'link' => 'https://updated.com',
        ]);
    }

    public function test_destroy_deletes_link_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $link = LinkTeam::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('link.destroy', $link));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('link_teams', [
            'id' => $link->id,
        ]);
    }
}

