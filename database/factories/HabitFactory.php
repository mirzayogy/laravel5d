<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Habit>
 */
class HabitFactory extends Factory
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
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'target_count' => fake()->numberBetween(1, 8),
            'unit' => fake()->randomElement(['times', 'glasses', 'pages', 'minutes']),
            'is_active' => true,
        ];
    }
}
