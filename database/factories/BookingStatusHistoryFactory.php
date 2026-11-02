<?php

namespace Database\Factories;


use App\Models\BookingStatusHistory;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingStatusHistoryFactory extends Factory
{
    protected $model = BookingStatusHistory::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'changed_by' => User::factory(),
            'old_status' => null,
            'new_status' => 'pending',
            'notes' => 'Booking dibuat.',
        ];
    }
}