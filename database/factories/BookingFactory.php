<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $price = fake()->randomElement([500000, 650000, 800000, 1000000]);
        $duration = fake()->numberBetween(3, 12);
        $start = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'room_id' => Room::factory(),
            'tenant_id' => Tenant::factory(),
            'code' => 'BK-' . strtoupper(fake()->unique()->bothify('??####')),
            'rental_type' => 'monthly',
            'start_date' => $start->format('Y-m-d'),
            'end_date' => (clone $start)->modify("+{$duration} months")->format('Y-m-d'),
            'duration' => $duration,
            'price_per_unit' => $price,
            'deposit_amount' => $price,
            'total_price' => $price * $duration,
            'status' => fake()->randomElement(['pending', 'confirmed', 'active', 'completed', 'cancelled']),
        ];
    }
}