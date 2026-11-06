<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-'.Str::upper(Str::random(8)),
            'status' => 'pending',
            'note' => fake()->boolean(30) ? fake()->sentence() : null,
            'ordered_at' => now(),
        ];
    }
}
