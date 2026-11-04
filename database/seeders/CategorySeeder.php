<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; // Baris ini wajib ada agar tidak error "Not Found"

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Salary', 'type' => 'income', 'icon' => '💰'],
            ['name' => 'Freelance', 'type' => 'income', 'icon' => '💻'],
            ['name' => 'Food & Dining', 'type' => 'expense', 'icon' => '🍔'],
            ['name' => 'Transportation', 'type' => 'expense', 'icon' => '🚗'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}