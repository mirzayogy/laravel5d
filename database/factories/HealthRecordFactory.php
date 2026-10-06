<?php

namespace Database\Factories;

use App\Models\Bird;
use App\Models\HealthRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HealthRecord>
 */
class HealthRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['checkup', 'vaccination', 'treatment', 'molting']);

        return [
            'bird_id' => Bird::factory(),
            'checked_on' => fake()->dateTimeBetween('-1 year', 'now'),
            'type' => $type,
            'weight_grams' => fake()->numberBetween(12, 70),
            'diagnosis' => $type === 'treatment'
                ? fake()->randomElement(['Respiratory infection', 'Feather mites', 'Dehydration', 'Stress'])
                : null,
            'notes' => fake()->optional(0.4)->sentence(),
        ];
    }
}
