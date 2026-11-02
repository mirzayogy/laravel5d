<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'method' => fake()->randomElement([
                'cash', 'bank_transfer', 'ewallet'
            ]),
            'amount' => 75000,
            'paid_at' => null,
            'proof_image' => null,
            'status' => 'unpaid',
            'verified_by' => null,
            'verified_at' => null,
            'rejection_reason' => null,
        ];
    }
}