<?php

namespace Database\Factories;

use App\Models\MoodEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MoodEntry>
 */
class MoodEntryFactory extends Factory
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
            'entry_date' => fake()->unique()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
            'mood_level' => fake()->numberBetween(1, 5),
            'note' => fake()->boolean(40) ? fake()->sentence() : null,
        ];
    }
}
