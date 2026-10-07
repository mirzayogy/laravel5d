<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Main Course', 'slug' => 'main-course'],
            ['name' => 'Desserts & Pastry', 'slug' => 'desserts-pastry'],
            ['name' => 'Beverages', 'slug' => 'beverages'],
            ['name' => 'Appetizers', 'slug' => 'appetizers'],
            ['name' => 'Healthy Food', 'slug' => 'healthy-food'],
            ['name' => 'Quick & Easy', 'slug' => 'quick-easy'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        $users = User::factory(5)->create()->each(function ($user) {
            $user->profile()->create([
                'bio' => 'This is a sample bio.',
                'phone' => '+1234567890',
            ]);
        });

        $ingredients = Ingredient::factory(10)->create();

        $allCategories = Category::all();

        $users->each(function ($user) use ($allCategories, $ingredients) {
            $recipes = Recipe::factory(2)->create([
                'user_id' => $user->id,
                'category_id' => $allCategories->random()->id,
            ]);

            foreach ($recipes as $recipe) {
                // Attach random ingredients to recipe
                $recipe->ingredients()->attach(
                    $ingredients->random(3)->pluck('id'),
                    ['quantity' => '100g']
                );
            }
        });

        $allRecipes = Recipe::all();

        foreach ($users as $user) {
            $randomRecipe = $allRecipes->random();

            Review::factory()->create([
                'user_id' => $user->id,
                'recipe_id' => $randomRecipe->id,
            ]);

            $user->favorites()->attach($randomRecipe->id);
        }
    }
}
