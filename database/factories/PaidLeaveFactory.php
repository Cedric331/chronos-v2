<?php

namespace Database\Factories;

use App\Models\PaidLeave;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaidLeave>
 */
class PaidLeaveFactory extends Factory
{
    protected $model = PaidLeave::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'team_id' => Team::factory(),
            'type' => 'Congés payés',
            'status' => 'En attente', // Les valeurs valides sont : 'En attente', 'Accepté', 'Refusé'
            'comment' => fake()->optional()->sentence(),
        ];
    }
}

