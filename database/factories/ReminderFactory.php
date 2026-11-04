<?php

namespace Database\Factories;

use App\Models\Habit;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reminder>
 */
class ReminderFactory extends Factory
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
            'remind_at' => fake()->time('H:i:s'),
            'days_of_week' => fake()->randomElements([1, 2, 3, 4, 5, 6, 7], fake()->numberBetween(3, 7)),
            'is_enabled' => true,
        ];
    }
}
