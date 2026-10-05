<?php

namespace Database\Factories;

use App\Models\FocusSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FocusSession>
 */
class FocusSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-7 days', 'now');

        return [
            'start_time' => $start,
            'end_time' => fake()->dateTimeBetween($start, 'now'),
            'duration_minutes' => fake()->numberBetween(15, 120),
            'status' => fake()->randomElement([
                'active',
                'completed',
                'cancelled',
            ]),
        ];
    }
}
