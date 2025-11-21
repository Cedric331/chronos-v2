<?php

namespace Database\Factories;

use App\Models\EventPlanning;
use App\Models\Planning;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventPlanning>
 */
class EventPlanningFactory extends Factory
{
    protected $model = EventPlanning::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'planning_id' => Planning::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->optional()->paragraph(),
        ];
    }
}

