<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category' => 'Beras',
                'name' => 'Beras Premium 5 Kg',
                'description' => 'Beras premium berkualitas untuk kebutuhan keluarga.',
                'price' => 75000,
                'stock' => 20,
                'image' => 'beras.jpg',
            ],
            [
                'category' => 'Beras',
                'name' => 'Beras Medium 5 Kg',
                'description' => 'Beras medium dengan kualitas baik dan harga terjangkau.',
                'price' => 65000,
                'stock' => 30,
                'image' => 'beras-medium.jpg',
            ],
            [
                'category' => 'Minyak Goreng',
                'name' => 'Minyak Goreng 1 Liter',
                'description' => 'Minyak goreng untuk kebutuhan memasak sehari-hari.',
                'price' => 18000,
                'stock' => 25,
                'image' => 'minyak.jpg',
            ],
            [
                'category' => 'Minyak Goreng',
                'name' => 'Minyak Goreng 2 Liter',
                'description' => 'Minyak goreng kemasan 2 liter.',
                'price' => 35000,
                'stock' => 15,
                'image' => 'minyak-2liter.jpg',
            ],
            [
                'category' => 'Gula',
                'name' => 'Gula Pasir 1 Kg',
                'description' => 'Gula pasir putih untuk kebutuhan rumah tangga.',
                'price' => 17000,
                'stock' => 30,
                'image' => 'gula.jpg',
            ],
            [
                'category' => 'Mie Instan',
                'name' => 'Mie Instan Goreng',
                'description' => 'Mie instan rasa goreng.',
                'price' => 3500,
                'stock' => 50,
                'image' => 'mie-goreng.jpg',
            ],
            [
                'category' => 'Mie Instan',
                'name' => 'Mie Instan Kuah',
                'description' => 'Mie instan dengan berbagai pilihan rasa kuah.',
                'price' => 3500,
                'stock' => 50,
                'image' => 'mie-kuah.jpg',
            ],
            [
                'category' => 'Telur',
                'name' => 'Telur Ayam 1 Kg',
                'description' => 'Telur ayam segar untuk kebutuhan sehari-hari.',
                'price' => 28000,
                'stock' => 20,
                'image' => 'telur.jpg',
            ],
        ];

        foreach ($products as $product) {
            $category = Category::where(
                'name',
                $product['category']
            )->firstOrFail();

            Product::updateOrCreate(
                ['name' => $product['name']],
                [
                    'category_id' => $category->id,
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'image' => $product['image'],
                    'created_at' => now(),
                ]
            );
        }
    }
}
