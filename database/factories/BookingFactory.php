<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Pet;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+30 days');
        $end = (clone $start)->modify('+60 minutes');

        return [
            'booking_code' => 'PAW-'.Str::upper(Str::random(12)),
            'user_id' => null,
            'pet_id' => Pet::factory(),
            'service_id' => Service::factory(),
            'groomer_id' => null,
            'scheduled_at' => $start,
            'ends_at' => $end,
            'total_price' => 75000,
            'status' => 'pending',
            'customer_notes' => fake()->optional()->sentence(),
            'rejection_reason' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Booking $booking) {
            if ($booking->user_id === null && $booking->pet_id) {
                $pet = Pet::find($booking->pet_id);

                if ($pet) {
                    $booking->user_id = $pet->user_id;
                }
            }
        });
    }
}