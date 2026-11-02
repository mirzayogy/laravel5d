<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // Mengambil akun customer
        $customer = User::where(
            'email',
            'customer@gmail.com'
        )->firstOrFail();

        // Mengambil produk gula
        $product = Product::where(
            'name',
            'Gula Pasir 1 Kg'
        )->firstOrFail();

        // Membuat contoh keranjang belanja
        Cart::updateOrCreate(
            [
                'user_id' => $customer->id,
                'product_id' => $product->id,
            ],
            [
                'quantity' => 2,
                'created_at' => now(),
            ]
        );
    }
}
