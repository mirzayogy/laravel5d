<?php

namespace Database\Factories;

use App\Enums\CycleGoal;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
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
            'birth_date' => fake()->dateTimeBetween('-40 years', '-15 years'),
            'goal' => fake()->randomElement(CycleGoal::cases()),
            'avg_cycle_length' => fake()->numberBetween(24, 35),
            'avg_period_length' => fake()->numberBetween(3, 7),
            'timezone' => 'Asia/Jakarta',
        ];
    }
}
