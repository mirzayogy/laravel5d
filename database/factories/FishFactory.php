<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Fish;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fish>
 */
class FishFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'name' => fake()->words(2, true).' Fish',
            'description' => fake()->sentence(),
            'stock' => fake()->numberBetween(10, 100),
            'unit' => fake()->randomElement(['ekor', 'kg', 'paket', 'unit']),
            'is_active' => true,
        ];
    }
}
