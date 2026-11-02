<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recipe_id' => Recipe::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence(8),
        ];
    }
}
