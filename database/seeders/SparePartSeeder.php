<?php

namespace Database\Seeders;

use App\Models\SparePart;
use Illuminate\Database\Seeder;

class SparePartSeeder extends Seeder
{
    public function run(): void
    {
        $parts = [
            [
                'part_code' => 'PRT-10001',
                'name' => 'Oli Mesin Mobil 1 5W-30 (4 Liter)',
                'brand' => 'Mobil 1',
                'unit_price' => 520000,
                'stock_quantity' => 45,
                'compatibility' => 'Universal Bensin',
            ],
            [
                'part_code' => 'PRT-10002',
                'name' => 'Filter Oli Genuine Parts',
                'brand' => 'Toyota Genuine Parts',
                'unit_price' => 55000,
                'stock_quantity' => 80,
                'compatibility' => 'Avanza, Innova, Rush, Yaris',
            ],
            [
                'part_code' => 'PRT-10003',
                'name' => 'Kampas Rem Depan Ceramic Brake Pads',
                'brand' => 'Brembo',
                'unit_price' => 420000,
                'stock_quantity' => 25,
                'compatibility' => 'Honda Jazz, Brio, City, HR-V',
            ],
            [
                'part_code' => 'PRT-10004',
                'name' => 'Busi Iridium Tough (4 Pcs)',
                'brand' => 'Denso',
                'unit_price' => 360000,
                'stock_quantity' => 30,
                'compatibility' => 'Universal',
            ],
            [
                'part_code' => 'PRT-10005',
                'name' => 'Air Radiator Super Long Life Coolant (4L)',
                'brand' => 'Prestone',
                'unit_price' => 110000,
                'stock_quantity' => 60,
                'compatibility' => 'Universal',
            ],
            [
                'part_code' => 'PRT-10006',
                'name' => 'Filter Udara Mesin Engine Air Filter',
                'brand' => 'Sakura',
                'unit_price' => 85000,
                'stock_quantity' => 40,
                'compatibility' => 'Xpander, Livina, Ertiga',
            ],
            [
                'part_code' => 'PRT-10007',
                'name' => 'Aki Kering Maintenance Free 45Ah',
                'brand' => 'GS Astra',
                'unit_price' => 920000,
                'stock_quantity' => 15,
                'compatibility' => 'Universal',
            ],
        ];

        foreach ($parts as $part) {
            SparePart::create($part);
        }
    }
}
