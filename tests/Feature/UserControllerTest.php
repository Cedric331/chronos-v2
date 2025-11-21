<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\SetUpRoles;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, SetUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
        Mail::fake();
    }

    public function test_store_creates_new_user(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $response = $this->actingAs($user)->post(route('user.store'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'team_id' => $team->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'team_id' => $team->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $response = $this->actingAs($user)->post(route('user.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'team_id']);
    }

    public function test_update_modifies_user(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $targetUser = User::factory()->create(['team_id' => $team->id, 'company_id' => $user->company_id]);

        $response = $this->actingAs($user)->patch(route('user.update', $targetUser), [
            'name' => 'Updated Name',
            'email' => $targetUser->email,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_destroy_deletes_user(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $targetUser = User::factory()->create(['team_id' => $team->id, 'company_id' => $user->company_id]);

        $response = $this->actingAs($user)->delete(route('user.delete', $targetUser));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }
}

