<?php

namespace Database\Factories;

use App\Models\UserStatistic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserStatistic>
 */
class UserStatisticFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->unique()->dateTimeBetween(
                '-30 days',
                'now'
            )->format('Y-m-d'),

            'completed_tasks' => fake()->numberBetween(0, 10),
            'delayed_tasks' => fake()->numberBetween(0, 5),
            'focus_minutes' => fake()->numberBetween(0, 300),
            'productivity_score' => fake()->randomFloat(2, 0, 100),
            'current_streak' => fake()->numberBetween(0, 15),
        ];
    }
}
