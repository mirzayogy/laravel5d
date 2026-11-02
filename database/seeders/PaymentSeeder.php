<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Order;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $order = Order::firstOrFail();

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'payment_method' => 'transfer_bank',
                'amount' => $order->total_amount,
                'payment_status' => 'paid',
                'payment_date' => now(),
                'created_at' => now(),
            ]
        );
    }
}