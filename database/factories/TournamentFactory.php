<?php

namespace Database\Factories;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
{
    $start = now()->addDays(fake()->numberBetween(7, 30));

    return [
        'name' => 'Piala ' . fake()->words(2, true),
        'start_date' => $start,
        'end_date' => $start->copy()->addDays(7),
        'registration_fee' => fake()->randomElement([100000, 250000, 500000]),
        'status' => 'open',
    ];
}
}
