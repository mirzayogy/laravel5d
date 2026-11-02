<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\MealPlan;
use App\Models\Profile;
use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            IngredientSeeder::class,
        ]);

        // Users + profiles (one-to-one)
        $users = User::factory(4)->create();
        $users->push(User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]));
        foreach ($users as $user) {
            Profile::factory()->create(['user_id' => $user->id]);
        }

        $ingredients = Ingredient::all();
        $categoryIds = Category::pluck('id');

        // Recipes with steps, ingredients (pivot data), and reviews
        $recipes = collect();
        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) {
                $recipe = Recipe::factory()->create([
                    'user_id' => $user->id,
                    'category_id' => $categoryIds->random(),
                ]);
                $recipes->push($recipe);

                $stepCount = rand(3, 5);
                for ($n = 1; $n <= $stepCount; $n++) {
                    RecipeStep::factory()->create([
                        'recipe_id' => $recipe->id,
                        'step_number' => $n,
                    ]);
                }

                $attach = [];
                foreach ($ingredients->random(4) as $ingredient) {
                    $attach[$ingredient->id] = [
                        'quantity' => rand(1, 6) * 50,
                        'unit' => $ingredient->default_unit ?? 'g',
                    ];
                }
                $recipe->ingredients()->attach($attach);

                // Reviews from other users (one review per user per recipe)
                foreach ($users->where('id', '!=', $user->id)->random(2) as $reviewer) {
                    Review::factory()->create([
                        'user_id' => $reviewer->id,
                        'recipe_id' => $recipe->id,
                    ]);
                }
            }
        }

        // Favorites and meal plans
        $mealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
        foreach ($users as $user) {
            $user->favoriteRecipes()->syncWithoutDetaching($recipes->random(3)->pluck('id')->all());

            $plan = MealPlan::factory()->create(['user_id' => $user->id]);
            for ($day = 0; $day < 5; $day++) {
                $plan->recipes()->attach($recipes->random()->id, [
                    'planned_date' => $plan->start_date->copy()->addDays($day)->toDateString(),
                    'meal_type' => $mealTypes[array_rand($mealTypes)],
                    'servings' => rand(1, 4),
                ]);
            }
        }
    }
}
