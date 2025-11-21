<?php

namespace Database\Factories;

use App\Models\LinkTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LinkTeam>
 */
class LinkTeamFactory extends Factory
{
    protected $model = LinkTeam::class;

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
            'link' => fake()->url(),
            'description' => fake()->optional()->sentence(),
        ];
    }
}

