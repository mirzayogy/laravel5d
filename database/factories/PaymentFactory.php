<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'type' => 'rent',
            'amount' => fake()->randomElement([500000, 650000, 800000, 1000000]),
            'due_date' => fake()->dateTimeBetween('-3 months', '+1 month')->format('Y-m-d'),
            'status' => 'unpaid',
        ];
    }
}