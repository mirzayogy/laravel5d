<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MenuFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(2, true);
        return [
            'merchant_id' => Merchant::factory(),
            'category_id' => Category::factory(),
            'name' => ucwords($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(10, 999),
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(15, 65) * 1000,
            'image' => null,
            'is_available' => true,
            'preparation_time_minutes' => fake()->randomElement([10, 15, 20, 25, 30]),
        ];
    }
}
