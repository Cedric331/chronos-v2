<?php

namespace Database\Factories;

use App\Models\TeamParams;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamParams>
 */
class TeamParamsFactory extends Factory
{
    protected $model = TeamParams::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_day' => json_encode(config('teams.type_days_default', [])),
            'send_coordinateur' => false,
            'share_link_planning' => false,
            'share_link' => false,
            'paid_leave' => true,
            'update_planning' => true,
            'module_alert' => false,
            'exchange_module' => false,
        ];
    }
}

