<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'seller_id' => User::where('role', 'seller')->inRandomOrder()->value('id') ?? User::factory()->seller(),
            'category_id' => Category::inRandomOrder()->value('id'),
            'brand_id' => Brand::inRandomOrder()->value('id'),
            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 99999),
            'sku' => strtoupper(fake()->unique()->bothify('PH-????-#####')),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(300, 25000) * 1000,
            'stock' => fake()->numberBetween(5, 50),
            'condition' => fake()->randomElement(['new', 'used']),
            'warranty_months' => fake()->randomElement([0, 6, 12, 24, 36]),
            'is_active' => true,
        ];
    }
}