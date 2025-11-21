<?php

namespace Database\Factories;

use App\Models\Companie;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'departement' => fake()->city(),
            'code_departement' => fake()->numberBetween(1, 99), // Code département français (01-99)
            'company_id' => Companie::factory(),
        ];
    }
}

