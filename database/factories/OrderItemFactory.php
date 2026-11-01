<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 3);
        $price = 25000;
        return [
            'order_id' => Order::factory(),
            'menu_id' => Menu::factory(),
            'quantity' => $qty,
            'unit_price' => $price,
            'subtotal' => $qty * $price,
            'special_notes' => fake()->optional()->randomElement(['Jangan pedas ya', 'Sambal dipisah', 'Sendok plastik ya']),
        ];
    }
}
