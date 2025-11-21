<?php

namespace Database\Factories;

use App\Models\Companie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Companie>
 */
class CompanieFactory extends Factory
{
    protected $model = Companie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->optional()->paragraph(),
            'logo' => null,
            'is_active' => true,
        ];
    }
}

