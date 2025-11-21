<?php

namespace Database\Factories;

use App\Models\Rotation;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rotation>
 */
class RotationFactory extends Factory
{
    protected $model = Rotation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->lexify('?????'), // Limité à 10 caractères dans la migration
        ];
    }
}

