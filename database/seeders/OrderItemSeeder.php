<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        // Mengambil pesanan customer
        $order = Order::firstOrFail();

        // Mengambil produk beras premium
        $product1 = Product::where(
            'name',
            'Beras Premium 5 Kg'
        )->firstOrFail();

        // Mengambil produk minyak goreng
        $product2 = Product::where(
            'name',
            'Minyak Goreng 1 Liter'
        )->firstOrFail();

        // Detail pesanan pertama
        OrderItem::updateOrCreate(
            [
                'order_id' => $order->id,
                'product_id' => $product1->id,
            ],
            [
                'quantity' => 1,
                'price' => $product1->price,
                'subtotal' => $product1->price * 1,
            ]
        );

        // Detail pesanan kedua
        OrderItem::updateOrCreate(
            [
                'order_id' => $order->id,
                'product_id' => $product2->id,
            ],
            [
                'quantity' => 1,
                'price' => $product2->price,
                'subtotal' => $product2->price * 1,
            ]
        );
    }
}
