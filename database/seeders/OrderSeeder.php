<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Mengambil akun customer
        $customer = User::where(
            'email',
            'customer@gmail.com'
        )->firstOrFail();

        // Membuat contoh pesanan
        Order::updateOrCreate(
            [
                'user_id' => $customer->id,
                'order_date' => '2026-11-02',
            ],
            [
                'total_amount' => 93000,
                'status' => 'processing',
                'created_at' => now(),
            ]
        );
    }
}
