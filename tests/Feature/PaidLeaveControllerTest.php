<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\PaidLeave;
use App\Models\Planning;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\SetUpRoles;
use Tests\TestCase;

class PaidLeaveControllerTest extends TestCase
{
    use RefreshDatabase, SetUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
        Mail::fake();
    }

    public function test_view_displays_paid_leaves(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->get(route('paidleave.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('leavesProps')
            ->has('yearsProps')
            ->has('usersProps')
        );
    }

    public function test_store_creates_paid_leave_request(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $calendar = Calendar::factory()->create(['date' => now()->addDay()]);
        $planning = Planning::factory()->create([
            'user_id' => $user->id,
            'calendar_id' => $calendar->id,
            'team_id' => $team->id,
        ]);

        $response = $this->actingAs($user)->post(route('paidleave.store'), [
            'days' => [
                [
                    'plannings' => [
                        ['calendar_id' => $calendar->id],
                    ],
                ],
            ],
            'type' => 'Congés payés',
            'comment' => 'Test comment',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('paid_leaves', [
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->post(route('paidleave.store'), []);

        $response->assertSessionHasErrors(['days', 'type']);
    }

    public function test_accepted_updates_paid_leave_status(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $user->assignRole('Coordinateur');

        $paidLeave = PaidLeave::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'status' => 'En attente', // Les valeurs valides sont : 'En attente', 'Accepté', 'Refusé'
        ]);

        $response = $this->actingAs($user)->post(route('paidleave.accepted'), [
            'id' => $paidLeave->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('paid_leaves', [
            'id' => $paidLeave->id,
            'status' => 'accepted',
        ]);
    }

    public function test_accepted_requires_coordinateur_role(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $paidLeave = PaidLeave::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $response = $this->actingAs($user)->post(route('paidleave.accepted'), [
            'id' => $paidLeave->id,
        ]);

        $response->assertStatus(403);
    }
}

