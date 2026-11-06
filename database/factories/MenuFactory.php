<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => Str::title(fake()->unique()->words(2, true)),
            'description' => fake()->sentence(),
            'price' => fake()->randomElement([8000, 12000, 15000, 20000, 25000]),
            'is_available' => true,
        ];
    }
}
