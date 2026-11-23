<?php

namespace Database\Factories;

use App\Models\Challenge;
use App\Models\Hint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hint>
 */
class HintFactory extends Factory
{
    public function definition(): array
    {
        return [
            'challenge_id' => Challenge::factory(),
            'content' => fake()->sentence(),
            'cost' => fake()->randomElement([10, 25, 50]),
        ];
    }
}
