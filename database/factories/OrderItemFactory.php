<?php

namespace Database\Factories;

use App\Models\LaundryOrder;
use App\Models\OrderItem;
use App\Models\ServiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $qty = $this->faker->numberBetween(1, 5);
        $price = 10000;

        return [
            'laundry_order_id' => LaundryOrder::factory(),
            'service_item_id' => ServiceItem::factory(),
            'qty' => $qty,
            'price_snapshot' => $price,
            'subtotal' => $qty * $price,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
