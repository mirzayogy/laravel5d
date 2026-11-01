<?php

namespace Database\Factories;

use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'merchant_id' => Merchant::factory(),
            'rating' => fake()->numberBetween(4, 5),
            'comment' => fake()->sentence(8),
            'merchant_reply' => fake()->optional()->sentence(6),
        ];
    }
}
