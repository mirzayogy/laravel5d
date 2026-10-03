<?php

namespace Database\Factories;

use App\Enums\MedicationType;
use App\Models\Medication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medication>
 */
class MedicationFactory extends Factory
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
            'name' => fake()->word(),
            'type' => fake()->randomElement(MedicationType::cases()),
            'dosage' => fake()->optional()->randomElement(['1 tablet', '2 tablet', '500 mg']),
            'is_active' => true,
        ];
    }
}
