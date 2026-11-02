<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Sarapan', 'Makan Siang', 'Makan Malam', 'Camilan', 'Minuman', 'Dessert'];

        foreach ($names as $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
