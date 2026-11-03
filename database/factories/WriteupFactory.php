<?php

namespace Database\Factories;

use App\Models\Challenge;
use App\Models\User;
use App\Models\Writeup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Writeup>
 */
class WriteupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'challenge_id' => Challenge::factory(),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(3, true),
            'status' => fake()->randomElement(['draft', 'published']),
        ];
    }
}
