<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            ['Rice', 'g'], ['Egg', 'pcs'], ['Chicken breast', 'g'], ['Tofu', 'g'],
            ['Tempeh', 'g'], ['Garlic', 'cloves'], ['Shallot', 'pcs'], ['Tomato', 'pcs'],
            ['Onion', 'pcs'], ['Carrot', 'g'], ['Spinach', 'g'], ['Potato', 'g'],
            ['Milk', 'ml'], ['Butter', 'g'], ['Flour', 'g'], ['Sugar', 'g'],
            ['Salt', 'tsp'], ['Cooking oil', 'ml'], ['Soy sauce', 'ml'], ['Banana', 'pcs'],
        ];

        foreach ($ingredients as [$name, $unit]) {
            Ingredient::updateOrCreate(['name' => $name], ['default_unit' => $unit]);
        }
    }
}
