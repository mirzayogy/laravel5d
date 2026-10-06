<?php

namespace Database\Factories;

use App\Models\Mechanic;
use App\Models\ServiceBooking;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceBookingFactory extends Factory
{
    protected $model = ServiceBooking::class;

    public function definition(): array
    {
        return [
            'booking_code' => 'SB-' . strtoupper(fake()->unique()->bothify('###???')),
            'vehicle_id' => Vehicle::factory(),
            'mechanic_id' => Mechanic::factory(),
            'booking_date' => fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'booking_time' => fake()->randomElement(['09:00:00', '10:30:00', '13:00:00', '14:30:00', '16:00:00']),
            'status' => fake()->randomElement(['Pending', 'Confirmed', 'In Progress', 'Completed', 'Cancelled']),
            'customer_notes' => fake()->optional()->sentence(),
        ];
    }
}
