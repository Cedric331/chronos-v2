<?php

namespace Tests\Feature;

use App\Enums\ExchangeRequestStatus;
use App\Models\Calendar;
use App\Models\ExchangeRequest;
use App\Models\Planning;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ExchangeRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_index_displays_exchange_requests_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $otherUser = User::factory()->create(['team_id' => $team->id, 'company_id' => $user->company_id]);

        $sentRequest = ExchangeRequest::factory()->create([
            'requester_id' => $user->id,
            'requested_id' => $otherUser->id,
            'team_id' => $team->id,
            'status' => ExchangeRequestStatus::PENDING->value,
        ]);

        $receivedRequest = ExchangeRequest::factory()->create([
            'requester_id' => $otherUser->id,
            'requested_id' => $user->id,
            'team_id' => $team->id,
            'status' => ExchangeRequestStatus::PENDING->value,
        ]);

        $response = $this->actingAs($user)->get(route('exchanges.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('sentRequests')
            ->has('receivedRequests')
        );
    }

    public function test_create_displays_form_with_team_users_and_plannings(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $otherUser = User::factory()->create(['team_id' => $team->id, 'company_id' => $user->company_id]);

        $calendar = Calendar::factory()->create(['date' => now()->addDay()]);
        $planning = Planning::factory()->create([
            'user_id' => $user->id,
            'calendar_id' => $calendar->id,
            'team_id' => $team->id,
        ]);

        $response = $this->actingAs($user)->get(route('exchanges.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('teamUsers')
            ->has('userPlannings')
        );
    }

    public function test_store_creates_exchange_request(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $otherUser = User::factory()->create(['team_id' => $team->id, 'company_id' => $user->company_id]);

        $calendar1 = Calendar::factory()->create(['date' => now()->addDay()]);
        $calendar2 = Calendar::factory()->create(['date' => now()->addDays(2)]);

        $requesterPlanning = Planning::factory()->create([
            'user_id' => $user->id,
            'calendar_id' => $calendar1->id,
            'team_id' => $team->id,
        ]);

        $requestedPlanning = Planning::factory()->create([
            'user_id' => $otherUser->id,
            'calendar_id' => $calendar2->id,
            'team_id' => $team->id,
        ]);

        $response = $this->actingAs($user)->post(route('exchanges.store'), [
            'requested_id' => $otherUser->id,
            'exchanges' => [
                [
                    'requester_planning_id' => $requesterPlanning->id,
                    'requested_planning_id' => $requestedPlanning->id,
                ],
            ],
            'requester_comment' => 'Test comment',
        ]);

        $response->assertRedirect(route('exchanges.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('exchange_requests', [
            'requester_id' => $user->id,
            'requested_id' => $otherUser->id,
            'requester_planning_id' => $requesterPlanning->id,
            'requested_planning_id' => $requestedPlanning->id,
            'status' => ExchangeRequestStatus::PENDING->value,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->post(route('exchanges.store'), []);

        $response->assertSessionHasErrors(['requested_id', 'exchanges']);
    }

    public function test_show_displays_exchange_request_details(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $otherUser = User::factory()->create(['team_id' => $team->id, 'company_id' => $user->company_id]);

        $exchange = ExchangeRequest::factory()->create([
            'requester_id' => $user->id,
            'requested_id' => $otherUser->id,
            'team_id' => $team->id,
        ]);

        $response = $this->actingAs($user)->get(route('exchanges.show', $exchange));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('exchange'));
    }

    public function test_show_prevents_unauthorized_access(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $otherTeam = Team::factory()->create(['company_id' => $otherUser->company_id]);

        $user->update(['team_id' => $team->id]);
        $otherUser->update(['team_id' => $otherTeam->id]);

        $exchange = ExchangeRequest::factory()->create([
            'requester_id' => $user->id,
            'requested_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $response = $this->actingAs($otherUser)->get(route('exchanges.show', $exchange));

        $response->assertStatus(403);
    }

    public function test_accept_updates_exchange_request_status(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $otherUser = User::factory()->create(['team_id' => $team->id, 'company_id' => $user->company_id]);

        $calendar1 = Calendar::factory()->create(['date' => now()->addDay()]);
        $calendar2 = Calendar::factory()->create(['date' => now()->addDays(2)]);

        $requesterPlanning = Planning::factory()->create([
            'user_id' => $otherUser->id,
            'calendar_id' => $calendar1->id,
            'team_id' => $team->id,
        ]);

        $requestedPlanning = Planning::factory()->create([
            'user_id' => $user->id,
            'calendar_id' => $calendar2->id,
            'team_id' => $team->id,
        ]);

        $exchange = ExchangeRequest::factory()->create([
            'requester_id' => $otherUser->id,
            'requested_id' => $user->id,
            'requester_planning_id' => $requesterPlanning->id,
            'requested_planning_id' => $requestedPlanning->id,
            'team_id' => $team->id,
            'status' => ExchangeRequestStatus::PENDING->value,
        ]);

        $response = $this->actingAs($user)->post(route('exchanges.accept', $exchange), [
            'requested_comment' => 'Accepted',
        ]);

        $response->assertRedirect(route('exchanges.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('exchange_requests', [
            'id' => $exchange->id,
            'status' => ExchangeRequestStatus::ACCEPTED->value,
        ]);
    }

    public function test_accept_prevents_unauthorized_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);
        $otherUser->update(['team_id' => $team->id, 'company_id' => $user->company_id]);

        $exchange = ExchangeRequest::factory()->create([
            'requester_id' => $user->id,
            'requested_id' => $user->id,
            'team_id' => $team->id,
            'status' => ExchangeRequestStatus::PENDING->value,
        ]);

        $response = $this->actingAs($otherUser)->post(route('exchanges.accept', $exchange));

        $response->assertStatus(403);
    }

    public function test_reject_updates_exchange_request_status(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $otherUser = User::factory()->create(['team_id' => $team->id, 'company_id' => $user->company_id]);

        $exchange = ExchangeRequest::factory()->create([
            'requester_id' => $otherUser->id,
            'requested_id' => $user->id,
            'team_id' => $team->id,
            'status' => ExchangeRequestStatus::PENDING->value,
        ]);

        $response = $this->actingAs($user)->post(route('exchanges.reject', $exchange), [
            'requested_comment' => 'Rejected',
        ]);

        $response->assertRedirect(route('exchanges.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('exchange_requests', [
            'id' => $exchange->id,
            'status' => ExchangeRequestStatus::REJECTED->value,
        ]);
    }

    public function test_cancel_updates_exchange_request_status(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $otherUser = User::factory()->create(['team_id' => $team->id, 'company_id' => $user->company_id]);

        $exchange = ExchangeRequest::factory()->create([
            'requester_id' => $user->id,
            'requested_id' => $otherUser->id,
            'team_id' => $team->id,
            'status' => ExchangeRequestStatus::PENDING->value,
        ]);

        $response = $this->actingAs($user)->post(route('exchanges.cancel', $exchange));

        $response->assertRedirect(route('exchanges.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('exchange_requests', [
            'id' => $exchange->id,
            'status' => ExchangeRequestStatus::CANCELLED->value,
        ]);
    }
}

