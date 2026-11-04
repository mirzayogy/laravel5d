<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'INV-'.fake()->unique()->numerify('####'),
            'status' => fake()->randomElement(['pending', 'paid', 'shipped', 'completed', 'cancelled']),
            'total_amount' => 0,
            'ordered_at' => now(),
        ];
    }
}
