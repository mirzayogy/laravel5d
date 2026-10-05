<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::where('stock', '>', 0)->get();
        $shipping = 25000;

        foreach (User::where('role', 'buyer')->get() as $buyer) {
            $address = $buyer->addresses()->first();

            foreach (range(1, 2) as $i) {
                $order = Order::create([
                    'user_id' => $buyer->id,
                    'shipping_address_id' => $address->id,
                    'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                    'status' => 'paid',
                    'shipping_cost' => $shipping,
                    'total_amount' => 0,
                    'ordered_at' => now()->subDays(rand(1, 30)),
                ]);

                $items = $products->random(3);
                $sum = 0;

                foreach ($items as $product) {
                    $qty = rand(1, 2);
                    $line = $product->price * $qty;

                    $order->products()->attach($product->id, [
                        'quantity' => $qty,
                        'unit_price' => $product->price,
                        'subtotal' => $line,
                    ]);

                    $sum += $line;

                    Review::firstOrCreate(
                        ['user_id' => $buyer->id, 'product_id' => $product->id],
                        ['rating' => rand(3, 5), 'comment' => fake()->sentence()]
                    );
                }

                $total = $sum + $shipping;
                $order->update(['total_amount' => $total]);

                $order->payment()->create([
                    'method' => fake()->randomElement(['transfer', 'e-wallet', 'cod']),
                    'status' => 'paid',
                    'amount' => $total,
                    'reference' => strtoupper(Str::random(10)),
                    'paid_at' => $order->ordered_at,
                ]);
            }
        }
    }
}