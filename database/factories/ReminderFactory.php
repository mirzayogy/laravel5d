<?php

namespace Database\Factories;

use App\Enums\ReminderType;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reminder>
 */
class ReminderFactory extends Factory
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
            'medication_id' => null,
            'type' => fake()->randomElement(ReminderType::cases()),
            'days_before' => fake()->numberBetween(1, 3),
            'remind_at' => '08:00',
            'is_enabled' => true,
        ];
    }
}
