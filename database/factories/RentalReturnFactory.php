<?php

namespace Database\Factories;

use App\Models\Rental;
use App\Models\RentalReturn;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalReturnFactory extends Factory
{
    protected $model = RentalReturn::class;

    public function definition(): array
    {
        return [
            'rental_id' => Rental::factory(),
            'actual_return_date' => now()->format('Y-m-d'),
            'late_days' => 0,
            'penalty_fee' => 0,
            'condition_after' => 'Semua barang kembali lengkap tanpa kerusakan.',
            'return_status' => 'verified',
        ];
    }
}
