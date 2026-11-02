<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Mengambil akun customer
        $customer = User::where(
            'email',
            'customer@gmail.com'
        )->firstOrFail();

        // Mengambil produk beras premium
        $product = Product::where(
            'name',
            'Beras Premium 5 Kg'
        )->firstOrFail();

        // Membuat contoh ulasan
        Review::updateOrCreate(
            [
                'user_id' => $customer->id,
                'product_id' => $product->id,
            ],
            [
                'rating' => 5,
                'comment' => 'Kualitas beras bagus, bersih, dan sesuai dengan harga.',
                'created_at' => now(),
            ]
        );
    }
}
