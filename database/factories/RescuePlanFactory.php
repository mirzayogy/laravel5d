<?php

namespace Database\Factories;

use App\Models\RescuePlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RescuePlan>
 */
class RescuePlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $remainingMinutes = fake()->numberBetween(60, 300);
        $daysRemaining = fake()->numberBetween(1, 7);

        return [
            'remaining_minutes' => $remainingMinutes,
            'days_remaining' => $daysRemaining,
            'daily_target_minutes' => (int) ceil(
                $remainingMinutes / $daysRemaining
            ),
            'recommendation' => 'Kerjakan tugas secara bertahap setiap hari.',
        ];
    }
}
