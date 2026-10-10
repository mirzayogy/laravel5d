<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanDetailFactory extends Factory
{
    protected $model = LoanDetail::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 3);
        $rate = fake()->numberBetween(50000, 300000);

        return [
            'loan_id' => Loan::factory(),
            'book_id' => Book::factory(),
            'quantity' => $qty,
            'fee_per_day' => $rate,
            'subtotal' => $qty * $rate * 3,
            'condition_out' => 'Good',
        ];
    }
}
