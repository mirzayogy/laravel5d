<?php

namespace Database\Factories;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_code' => 'LB-' . date('Ymd') . '-' . strtoupper(fake()->unique()->bothify('###??')),
            'user_id' => User::factory(),
            'merchant_id' => Merchant::factory(),
            'total_amount' => 50000,
            'delivery_fee' => 10000,
            'status' => 'delivered',
            'delivery_address' => fake()->address(),
            'payment_method' => fake()->randomElement(['qris', 'cash', 'bank_transfer']),
            'payment_status' => 'paid',
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
