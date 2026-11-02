<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Beras',
                'description' => 'Berbagai jenis beras untuk kebutuhan sehari-hari.',
            ],
            [
                'name' => 'Minyak Goreng',
                'description' => 'Produk minyak goreng berbagai merek dan ukuran.',
            ],
            [
                'name' => 'Gula',
                'description' => 'Berbagai jenis gula untuk kebutuhan rumah tangga.',
            ],
            [
                'name' => 'Mie Instan',
                'description' => 'Aneka produk mie instan berbagai rasa.',
            ],
            [
                'name' => 'Telur',
                'description' => 'Produk telur untuk kebutuhan konsumsi.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
