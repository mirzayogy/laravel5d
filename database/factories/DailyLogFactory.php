<?php

namespace Database\Factories;

use App\Models\DailyLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyLog>
 */
class DailyLogFactory extends Factory
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
            'log_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'basal_temp' => fake()->randomFloat(2, 36, 37.5),
            'weight' => fake()->randomFloat(2, 40, 80),
            'water_ml' => fake()->numberBetween(500, 3000),
            'sleep_hours' => fake()->randomFloat(1, 4, 10),
        ];
    }
}
