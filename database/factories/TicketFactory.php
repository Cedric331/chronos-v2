<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status_id' => TicketStatus::factory(),
            'category_id' => TicketCategory::factory(),
            'priority_id' => TicketPriority::factory(),
            'created_by' => User::factory(),
            'team_id' => Team::factory(),
            'assigned_to' => null,
            'due_date' => null,
            'closed_at' => null,
        ];
    }
}

