<?php

namespace Database\Factories;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HabitLog>
 */
class HabitLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'habit_id' => Habit::factory(),
            'logged_date' => fake()->unique()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
            'value' => fake()->numberBetween(1, 8),
            'note' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }
}
