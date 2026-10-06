<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promos = [
            [
                'promo_code' => 'SERVICEBERKAH10',
                'title' => 'Diskon Servis Rutin 10%',
                'discount_percentage' => 10,
                'max_discount_amount' => 100000,
                'min_transaction_amount' => 200000,
                'valid_until' => now()->addMonths(2)->format('Y-m-d'),
                'is_active' => true,
            ],
            [
                'promo_code' => 'ACSEJUK20',
                'title' => 'Promo Flushing AC & Freon 20%',
                'discount_percentage' => 20,
                'max_discount_amount' => 150000,
                'min_transaction_amount' => 300000,
                'valid_until' => now()->addMonths(1)->format('Y-m-d'),
                'is_active' => true,
            ],
            [
                'promo_code' => 'NEWCUSTOMER50K',
                'title' => 'Potongan Langsung 50 Ribu Booking Pertama',
                'discount_percentage' => 15,
                'max_discount_amount' => 50000,
                'min_transaction_amount' => 150000,
                'valid_until' => now()->addMonths(3)->format('Y-m-d'),
                'is_active' => true,
            ],
        ];

        foreach ($promos as $promo) {
            Promotion::create($promo);
        }
    }
}
