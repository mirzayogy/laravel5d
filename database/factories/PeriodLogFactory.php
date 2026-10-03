<?php

namespace Database\Factories;

use App\Enums\FlowLevel;
use App\Models\Cycle;
use App\Models\PeriodLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PeriodLog>
 */
class PeriodLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'cycle_id' => Cycle::factory(),
            'log_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'flow_level' => fake()->randomElement(FlowLevel::cases()),
        ];
    }
}
