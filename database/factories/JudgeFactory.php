<?php

namespace Database\Factories;

use App\Models\Judge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Judge>
 */
class JudgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake('id_ID')->firstName().' '.fake('id_ID')->lastName(),
            'license_number' => fake()->unique()->numerify('JDG-######'),
            'city' => fake()->optional(0.8)->randomElement([
                'Banjarbaru', 'Banjarmasin', 'Palangka Raya', 'Samarinda', 'Pontianak',
            ]),
            'years_of_experience' => fake()->numberBetween(1, 25),
        ];
    }
}
