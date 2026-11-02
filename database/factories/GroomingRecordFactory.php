<?php

namespace Database\Factories;

use App\Models\GroomingRecord;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroomingRecordFactory extends Factory
{
    protected $model = GroomingRecord::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'groomer_id' => User::factory()->state([
                'role' => 'groomer',
            ]),
            'notes' => fake()->sentence(),
            'condition_notes' => fake()->sentence(),
            'completed_at' => now(),
        ];
    }
}