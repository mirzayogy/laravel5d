<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'deadline' => fake()->dateTimeBetween('now', '+14 days'),
            'difficulty' => fake()->randomElement([
                'easy',
                'medium',
                'hard',
            ]),
            'estimated_minutes' => fake()->numberBetween(30, 240),
            'priority_score' => fake()->randomFloat(2, 1, 100),
            'status' => fake()->randomElement([
                'pending',
                'in_progress',
                'completed',
                'delayed',
            ]),
            'completed_at' => null,
        ];
    }
}
