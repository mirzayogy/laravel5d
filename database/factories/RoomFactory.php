<?php

namespace Database\Factories;

use App\Models\Kost;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        $month = fake()->randomElement([500000, 650000, 800000, 1000000]);

        return [
            'kost_id' => Kost::factory(),
            'room_number' => (string) fake()->unique()->numberBetween(101, 599),
            'floor' => fake()->numberBetween(1, 3),
            'size_m2' => fake()->randomElement([9, 12, 15]),
            'price_per_day' => (int) (round($month / 30 / 1000) * 1000),
            'price_per_month' => $month,
            'status' => fake()->randomElement(['available', 'occupied', 'maintenance']),
        ];
    }
}