<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BirthdayControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_birthdays_returns_birthdays(): void
    {
        $user = User::factory()->create(['birthday' => now()->addDays(10)]);
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->getJson('/api/birthdays', [
            'daysAhead' => 30,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['birthdays']);
    }

    public function test_get_birthdays_validates_days_ahead_parameter(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->getJson('/api/birthdays', [
            'daysAhead' => 500, // Invalid: exceeds max
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['daysAhead']);
    }
}

