<?php

namespace Database\Factories;

use App\Models\CustomerReview;
use App\Models\SalesReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesReport>
 */
class SalesReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'customer_review_id' => CustomerReview::factory(),
            'entry_date' => fake()->date(),
            'title' => fake()->sentence(3),
            'content' => fake()->paragraph(),
        ];
    }
}
