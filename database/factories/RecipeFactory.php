<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::inRandomOrder()->value('id') ?? Category::query()->create([
                'name' => 'General',
                'slug' => 'general-' . fake()->unique()->numberBetween(1, 9999),
            ])->id,
            'title' => ucfirst(fake()->words(3, true)),
            'description' => fake()->paragraph(),
            'servings' => fake()->numberBetween(1, 6),
            'prep_minutes' => fake()->numberBetween(5, 30),
            'cook_minutes' => fake()->numberBetween(10, 60),
            'calories_per_serving' => fake()->numberBetween(150, 700),
            'is_public' => true,
        ];
    }
}
