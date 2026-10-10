<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\LoanReturn;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanReturnFactory extends Factory
{
    protected $model = LoanReturn::class;

    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'actual_return_date' => now()->format('Y-m-d'),
            'late_days' => 0,
            'penalty_fee' => 0,
            'condition_after' => 'Semua barang kembali lengkap tanpa kerusakan.',
            'return_status' => 'verified',
        ];
    }
}
