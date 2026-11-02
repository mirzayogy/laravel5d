<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Breakfast', 'slug' => 'breakfast'],
            ['name' => 'Lunch', 'slug' => 'lunch'],
            ['name' => 'Dinner', 'slug' => 'dinner'],
            ['name' => 'Snacks', 'slug' => 'snacks'],
            ['name' => 'Drinks', 'slug' => 'drinks'],
            ['name' => 'Desserts', 'slug' => 'desserts'],
            ['name' => 'Vegetarian', 'slug' => 'vegetarian'],
            ['name' => 'Healthy & Low Calorie', 'slug' => 'healthy-low-calorie'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
