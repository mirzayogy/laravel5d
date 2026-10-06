<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\RentalItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RentalItemFactory extends Factory
{
    protected $model = RentalItem::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 999),
            'brand' => fake()->company(),
            'daily_rate' => fake()->numberBetween(50000, 750000),
            'stock_quantity' => fake()->numberBetween(1, 10),
            'status' => 'available',
            'description' => fake()->paragraph(),
        ];
    }
}
