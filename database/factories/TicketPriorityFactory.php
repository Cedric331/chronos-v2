<?php

namespace Database\Factories;

use App\Models\TicketPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketPriority>
 */
class TicketPriorityFactory extends Factory
{
    protected $model = TicketPriority::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Faible', 'Normale', 'Haute', 'Urgente']),
            'color' => fake()->hexColor(),
            'level' => fake()->numberBetween(1, 4),
        ];
    }
}

