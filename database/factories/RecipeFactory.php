<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);
        return [
            'user_id' => user::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(3),
            'prep_time_minutes' => fake()->numberBetween(10, 60),
            'cook_time_minutes' => fake()->numberBetween(15, 120),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'is_published' => true,
        ];
    }
}
