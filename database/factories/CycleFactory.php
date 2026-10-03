<?php

namespace Database\Factories;

use App\Models\Cycle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cycle>
 */
class CycleFactory extends Factory
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
            'start_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'end_date' => null,
            'cycle_length' => null,
            'period_length' => null,
        ];
    }
}
