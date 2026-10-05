<?php

namespace Database\Factories;

use App\Models\Subtask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subtask>
 */
class SubtaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'status' => fake()->randomElement([
                'pending',
                'completed',
            ]),
            'estimated_minutes' => fake()->numberBetween(10, 60),
            'order_number' => fake()->numberBetween(1, 5),
            'completed_at' => null,
        ];
    }
}
