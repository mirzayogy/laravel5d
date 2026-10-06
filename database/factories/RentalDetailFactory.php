<?php

namespace Database\Factories;

use App\Models\Rental;
use App\Models\RentalDetail;
use App\Models\RentalItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalDetailFactory extends Factory
{
    protected $model = RentalDetail::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 3);
        $rate = fake()->numberBetween(50000, 300000);

        return [
            'rental_id' => Rental::factory(),
            'rental_item_id' => RentalItem::factory(),
            'quantity' => $qty,
            'price_per_day' => $rate,
            'subtotal' => $qty * $rate * 3,
            'condition_before' => 'Sangat Baik',
        ];
    }
}
