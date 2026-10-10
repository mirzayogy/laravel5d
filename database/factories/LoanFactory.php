<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+3 days');

        return [
            'loan_code' => 'RNT-' . fake()->unique()->numerify('######'),
            'user_id' => User::factory(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_amount' => fake()->numberBetween(150000, 2000000),
            'status' => fake()->randomElement(['pending', 'active', 'completed']),
            'notes' => fake()->sentence(),
        ];
    }
}
