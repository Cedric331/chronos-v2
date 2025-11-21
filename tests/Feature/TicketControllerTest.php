<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_index_displays_tickets_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $status = TicketStatus::factory()->create(['is_default' => true]);
        $category = TicketCategory::factory()->create();
        $priority = TicketPriority::factory()->create();

        $ticket = Ticket::factory()->create([
            'created_by' => $user->id,
            'team_id' => $team->id,
            'status_id' => $status->id,
            'category_id' => $category->id,
            'priority_id' => $priority->id,
        ]);

        $response = $this->actingAs($user)->get(route('tickets.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('tickets'));
    }

    public function test_create_displays_ticket_creation_form(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        TicketStatus::factory()->create();
        TicketCategory::factory()->create();
        TicketPriority::factory()->create();

        $response = $this->actingAs($user)->get(route('tickets.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('statuses')
            ->has('categories')
            ->has('priorities')
        );
    }

    public function test_store_creates_new_ticket(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $status = TicketStatus::factory()->create(['is_default' => true]);
        $category = TicketCategory::factory()->create();
        $priority = TicketPriority::factory()->create();

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'title' => 'Test Ticket',
            'description' => 'Test Description',
            'category_id' => $category->id,
            'priority_id' => $priority->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'description' => 'Test Description',
            'created_by' => $user->id,
            'team_id' => $team->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $response = $this->actingAs($user)->post(route('tickets.store'), []);

        $response->assertSessionHasErrors(['title', 'description', 'category_id', 'priority_id']);
    }

    public function test_show_displays_ticket_details(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $status = TicketStatus::factory()->create();
        $category = TicketCategory::factory()->create();
        $priority = TicketPriority::factory()->create();

        $ticket = Ticket::factory()->create([
            'created_by' => $user->id,
            'team_id' => $team->id,
            'status_id' => $status->id,
            'category_id' => $category->id,
            'priority_id' => $priority->id,
        ]);

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('ticket'));
    }

    public function test_show_prevents_unauthorized_access(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $otherTeam = Team::factory()->create(['company_id' => $otherUser->company_id]);

        $user->update(['team_id' => $team->id]);
        $otherUser->update(['team_id' => $otherTeam->id]);

        $status = TicketStatus::factory()->create();
        $category = TicketCategory::factory()->create();
        $priority = TicketPriority::factory()->create();

        $ticket = Ticket::factory()->create([
            'created_by' => $user->id,
            'team_id' => $team->id,
            'status_id' => $status->id,
            'category_id' => $category->id,
            'priority_id' => $priority->id,
        ]);

        $response = $this->actingAs($otherUser)->get(route('tickets.show', $ticket));

        $response->assertStatus(403);
    }

    public function test_update_modifies_ticket(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $status = TicketStatus::factory()->create();
        $category = TicketCategory::factory()->create();
        $priority = TicketPriority::factory()->create();

        $ticket = Ticket::factory()->create([
            'created_by' => $user->id,
            'team_id' => $team->id,
            'status_id' => $status->id,
            'category_id' => $category->id,
            'priority_id' => $priority->id,
        ]);

        $newCategory = TicketCategory::factory()->create();

        $response = $this->actingAs($user)->put(route('tickets.update', $ticket), [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'category_id' => $newCategory->id,
            'priority_id' => $priority->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Updated Title',
            'category_id' => $newCategory->id,
        ]);
    }

    public function test_add_comment_creates_comment_on_ticket(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['company_id' => $user->company_id]);
        $user->update(['team_id' => $team->id]);

        $status = TicketStatus::factory()->create(['is_closed' => false]);
        $category = TicketCategory::factory()->create();
        $priority = TicketPriority::factory()->create();

        $ticket = Ticket::factory()->create([
            'created_by' => $user->id,
            'team_id' => $team->id,
            'status_id' => $status->id,
            'category_id' => $category->id,
            'priority_id' => $priority->id,
        ]);

        $response = $this->actingAs($user)->post(route('tickets.comments.store', $ticket), [
            'content' => 'Test comment',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'content' => 'Test comment',
        ]);
    }
}

