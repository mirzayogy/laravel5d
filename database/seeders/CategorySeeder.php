<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan Berat & Nasi', 'slug' => 'makanan-berat', 'icon' => 'bowl-rice', 'description' => 'Aneka olahan nasi, soto banjar, nasi kuning, dan lauk khas'],
            ['name' => 'Kue Tradisional & Wadai', 'slug' => 'kue-tradisional', 'icon' => 'cookie', 'description' => 'Wadai khas Banjar seperti bingka, amparan tatak, dan sari pengantin'],
            ['name' => 'Minuman & Kopi', 'slug' => 'minuman-kopi', 'icon' => 'mug-hot', 'description' => 'Es kelapa muda, kopi lokal, dan aneka minuman segar'],
            ['name' => 'Camilan & Gorengan', 'slug' => 'camilan', 'icon' => 'french-fries', 'description' => 'Kerupuk acan, pisang keju, dan camilan renyah UMKM'],
            ['name' => 'Paket Katering Acara', 'slug' => 'katering', 'icon' => 'boxes-packing', 'description' => 'Paket prasmanan dan nasi kotak untuk seminar dan tasyakuran'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
