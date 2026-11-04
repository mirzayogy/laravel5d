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
            'entry_date' => fake()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(2, true),
        ];
    }
}
