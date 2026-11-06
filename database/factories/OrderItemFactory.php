<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'menu_id' => Menu::factory(),
            'quantity' => fake()->numberBetween(1, 3),
            'unit_price' => fake()->randomElement([8000, 12000, 15000, 20000, 25000]),
        ];
    }
}
