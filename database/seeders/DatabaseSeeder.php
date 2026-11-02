<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'nazwa@example.com'],
            ['name' => 'Nazwa Aulia Rizka', 'password' => Hash::make('password')]
        );

        $this->call([
            CategorySeeder::class,
            IngredientSeeder::class,
            RecipeSeeder::class,
        ]);
    }
}
