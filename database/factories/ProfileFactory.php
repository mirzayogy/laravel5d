<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => fake()->sentence(10),
            'avatar' => null,
            'diet_preference' => fake()->randomElement(['none', 'vegetarian', 'low-carb', 'high-protein']),
            'daily_calorie_goal' => fake()->numberBetween(1500, 2500),
        ];
    }
}
