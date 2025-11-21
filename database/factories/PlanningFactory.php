<?php

namespace Database\Factories;

use App\Models\Calendar;
use App\Models\Planning;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Planning>
 */
class PlanningFactory extends Factory
{
    protected $model = Planning::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'calendar_id' => Calendar::factory(),
            'team_id' => Team::factory(),
            'type_day' => 'Planifié',
            'debut_journee' => '09:00',
            'fin_journee' => '18:00',
            'debut_pause' => '12:00',
            'fin_pause' => '13:00',
            'is_technician' => false,
            'telework' => false,
            'hours' => 7,
            'rotation_id' => null,
        ];
    }
}

