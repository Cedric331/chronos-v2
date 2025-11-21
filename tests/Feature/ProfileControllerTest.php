<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_edit_displays_profile_form(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('mustVerifyEmail'));
    }

    public function test_update_modifies_user_profile(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
        ]);

        $response->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_update_allows_avatar_upload(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertRedirect(route('profile.edit'));

        Storage::disk('public')->assertExists('avatar/'.$user->id.'/'.$file->hashName());
    }

    public function test_destroy_deletes_user_account(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_destroy_validates_password(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->delete(route('profile.destroy'), []);

        $response->assertSessionHasErrors(['password']);
    }
}

