<?php

namespace Database\Factories;

use App\Models\TaskDelay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskDelay>
 */
class TaskDelayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $oldDeadline = fake()->dateTimeBetween('now', '+5 days');

        return [
            'old_deadline' => $oldDeadline,
            'new_deadline' => fake()->dateTimeBetween(
                $oldDeadline,
                '+10 days'
            ),
            'reason' => fake()->sentence(),
            'delayed_at' => now(),
        ];
    }
}
