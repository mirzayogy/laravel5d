<?php

namespace Database\Factories;

use App\Models\CustomerReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerReview>
 */
class CustomerReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'entry_date' => fake()->date(),
            'rating' => fake()->numberBetween(4, 5),
            'note' => fake()->sentence(),
        ];
    }
}
