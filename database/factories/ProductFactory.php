<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(2, true),
            'sku' => strtoupper(fake()->unique()->bothify('???-###')),
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(10, 500) * 1000,
            'stock' => fake()->numberBetween(0, 100),
        ];
    }
}
