<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Challenge>
 */
class ChallengeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'points' => fake()->randomElement([100, 200, 300, 500]),
            'flag_hash' => Hash::make('FLAG{'.Str::random(12).'}'),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'is_published' => true,
        ];
    }
}
