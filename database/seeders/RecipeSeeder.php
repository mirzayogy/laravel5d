<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrFail();
        $categories = Category::pluck('id', 'name');
        $ingredients = Ingredient::pluck('id', 'name');

        $recipes = [
            [
                'title' => 'Nasi Goreng Sederhana',
                'category' => 'Sarapan',
                'description' => 'Nasi goreng cepat untuk sarapan.',
                'instructions' => "1. Haluskan bawang merah, bawang putih, dan cabai.\n2. Tumis bumbu halus hingga harum.\n3. Masukkan telur, orak-arik.\n4. Masukkan nasi, kecap manis, dan garam. Aduk rata.",
                'cooking_time' => 15,
                'servings' => 2,
                'difficulty' => 'mudah',
                'ingredients' => [
                    'Nasi putih' => 2, 'Telur' => 1, 'Bawang merah' => 3, 'Bawang putih' => 2,
                    'Cabai' => 2, 'Kecap manis' => 2, 'Garam' => 0.5, 'Minyak goreng' => 2,
                ],
            ],
            [
                'title' => 'Soto Ayam',
                'category' => 'Makan Siang',
                'description' => 'Soto ayam kuah kuning yang hangat.',
                'instructions' => "1. Rebus ayam dengan air hingga empuk.\n2. Haluskan bawang, kunyit, dan jahe lalu tumis.\n3. Masukkan bumbu ke kaldu, beri garam.\n4. Masak 15 menit, sajikan.",
                'cooking_time' => 60,
                'servings' => 4,
                'difficulty' => 'sedang',
                'ingredients' => [
                    'Ayam' => 500, 'Bawang merah' => 5, 'Bawang putih' => 3, 'Kunyit' => 2,
                    'Jahe' => 2, 'Garam' => 1, 'Air' => 1500, 'Minyak goreng' => 2,
                ],
            ],
            [
                'title' => 'Tumis Kangkung',
                'category' => 'Makan Malam',
                'description' => 'Sayur tumis pedas yang praktis.',
                'instructions' => "1. Tumis bawang merah, bawang putih, dan cabai.\n2. Masukkan kangkung, aduk cepat.\n3. Beri garam, angkat saat masih renyah.",
                'cooking_time' => 10,
                'servings' => 2,
                'difficulty' => 'mudah',
                'ingredients' => [
                    'Kangkung' => 1, 'Bawang merah' => 3, 'Bawang putih' => 3,
                    'Cabai' => 3, 'Garam' => 0.5, 'Minyak goreng' => 2,
                ],
            ],
            [
                'title' => 'Pisang Goreng',
                'category' => 'Camilan',
                'description' => 'Camilan manis dengan balutan tepung renyah.',
                'instructions' => "1. Campur tepung, gula, garam, dan air menjadi adonan.\n2. Celupkan pisang ke adonan.\n3. Goreng hingga keemasan.",
                'cooking_time' => 20,
                'servings' => 3,
                'difficulty' => 'mudah',
                'ingredients' => [
                    'Pisang' => 6, 'Tepung terigu' => 100, 'Gula pasir' => 2,
                    'Garam' => 0.5, 'Air' => 100, 'Minyak goreng' => 10,
                ],
            ],
            [
                'title' => 'Es Teh Manis',
                'category' => 'Minuman',
                'description' => 'Minuman klasik yang menyegarkan.',
                'instructions' => "1. Seduh teh celup dengan air panas.\n2. Larutkan gula pasir.\n3. Dinginkan dan sajikan dengan es batu.",
                'cooking_time' => 5,
                'servings' => 2,
                'difficulty' => 'mudah',
                'ingredients' => ['Teh celup' => 2, 'Gula pasir' => 3, 'Air' => 500],
            ],
        ];

        $created = [];

        foreach ($recipes as $data) {
            $recipe = Recipe::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'user_id' => $user->id,
                    'category_id' => $categories[$data['category']] ?? null,
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'instructions' => $data['instructions'],
                    'cooking_time' => $data['cooking_time'],
                    'servings' => $data['servings'],
                    'difficulty' => $data['difficulty'],
                ]
            );

            $sync = [];
            foreach ($data['ingredients'] as $name => $quantity) {
                $sync[$ingredients[$name]] = ['quantity' => $quantity];
            }
            $recipe->ingredients()->sync($sync);

            $created[] = $recipe->id;
        }

        // Dua resep pertama dijadikan favorit user contoh.
        $user->favoriteRecipes()->syncWithoutDetaching(array_slice($created, 0, 2));
    }
}
