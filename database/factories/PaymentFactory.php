<?php

namespace Database\Factories;

use App\Models\LaundryOrder;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'payment_code' => 'PAY-' . strtoupper($this->faker->bothify('#####??')),
            'laundry_order_id' => LaundryOrder::factory(),
            'amount' => 25000,
            'payment_method' => $this->faker->randomElement(['cash', 'qris', 'transfer']),
            'status' => 'paid',
            'paid_at' => now(),
        ];
    }
}
