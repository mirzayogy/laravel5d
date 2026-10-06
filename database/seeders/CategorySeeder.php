<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Mobil',
                'slug' => 'mobil',
                'description' => 'Rental kendaraan roda empat untuk perjalanan keluarga, dinas, dan wisata.',
                'icon' => 'car',
            ],
            [
                'name' => 'Sepeda Motor',
                'slug' => 'sepeda-motor',
                'description' => 'Rental sepeda motor matic, manual, dan trail untuk mobilitas cepat.',
                'icon' => 'motorcycle',
            ],
            [
                'name' => 'Kamera & Fotografi',
                'slug' => 'kamera-fotografi',
                'description' => 'Kamera DSLR, Mirrorless, lensa profesional, stabilizer, dan lighting fotografi.',
                'icon' => 'camera',
            ],
            [
                'name' => 'Alat Camping & Outdoor',
                'slug' => 'alat-camping-outdoor',
                'description' => 'Tenda dome, nesting, kompor portable, sleeping bag, carrier, dan perlengkapan gunung.',
                'icon' => 'tent',
            ],
            [
                'name' => 'Sound System & Audio',
                'slug' => 'sound-system-audio',
                'description' => 'Speaker aktif, wireless microphone, audio mixer, dan genset untuk acara indoor/outdoor.',
                'icon' => 'speaker',
            ],
            [
                'name' => 'Alat Konstruksi & Pertukangan',
                'slug' => 'alat-konstruksi',
                'description' => 'Bor baterai, mesin gerinda, trimmer kayu, scaffolding, dan perkakas teknis.',
                'icon' => 'wrench',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
