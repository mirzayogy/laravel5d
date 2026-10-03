<?php

namespace Database\Factories;

use App\Models\Symptom;
use App\Models\SymptomLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SymptomLog>
 */
class SymptomLogFactory extends Factory
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
            'symptom_id' => Symptom::factory(),
            'log_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'severity' => fake()->numberBetween(1, 3),
        ];
    }
}
