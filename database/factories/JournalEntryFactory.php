<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
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
            'mood_entry_id' => null,
            'entry_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'title' => fake()->sentence(3),
            'content' => fake()->paragraph(),
        ];
    }
}
