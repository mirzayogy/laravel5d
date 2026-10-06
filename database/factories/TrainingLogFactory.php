<?php

namespace Database\Factories;

use App\Models\Bird;
use App\Models\TrainingLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrainingLog>
 */
class TrainingLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bird_id' => Bird::factory(),
            'trained_on' => fake()->dateTimeBetween('-3 months', 'now'),
            'focus' => fake()->randomElement(['volume', 'mental', 'stamina', 'mimicry']),
            'duration_minutes' => fake()->numberBetween(10, 90),
            'notes' => fake()->optional(0.4)->sentence(),
        ];
    }
}
