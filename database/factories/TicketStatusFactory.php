<?php

namespace Database\Factories;

use App\Models\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketStatus>
 */
class TicketStatusFactory extends Factory
{
    protected $model = TicketStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'color' => fake()->hexColor(),
            'order' => fake()->numberBetween(1, 10),
            'is_default' => false,
            'is_closed' => false,
        ];
    }
}

