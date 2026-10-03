<?php

namespace Database\Factories;

use App\Models\Medication;
use App\Models\MedicationLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicationLog>
 */
class MedicationLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medication_id' => Medication::factory(),
            'taken_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'taken_time' => fake()->time('H:i'),
        ];
    }
}
