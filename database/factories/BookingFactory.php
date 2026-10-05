<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Field;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    $start = fake()->randomElement([8, 10, 16, 19]);

    return [
        'user_id' => User::factory(),
        'field_id' => Field::factory(),
        'booking_date' => now()->addDays(fake()->numberBetween(1, 30)),
        'start_time' => sprintf('%02d:00:00', $start),
        'end_time' => sprintf('%02d:00:00', $start + 2),
        'total_price' => fake()->randomElement([200000, 300000, 400000]),
        'status' => fake()->randomElement(['pending', 'confirmed', 'done']),
    ];
}
}
