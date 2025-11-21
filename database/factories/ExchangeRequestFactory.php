<?php

namespace Database\Factories;

use App\Enums\ExchangeRequestStatus;
use App\Models\ExchangeRequest;
use App\Models\Planning;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExchangeRequest>
 */
class ExchangeRequestFactory extends Factory
{
    protected $model = ExchangeRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_id' => User::factory(),
            'requested_id' => User::factory(),
            'requester_planning_id' => Planning::factory(),
            'requested_planning_id' => Planning::factory(),
            'team_id' => Team::factory(),
            'status' => ExchangeRequestStatus::PENDING->value,
            'requester_comment' => fake()->optional()->sentence(),
            'requested_comment' => null,
            'responded_at' => null,
        ];
    }
}

