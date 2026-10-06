<?php

namespace Database\Factories;

use App\Models\RentalItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'rental_item_id' => RentalItem::factory(),
            'rating' => fake()->numberBetween(4, 5),
            'comment' => fake()->sentence(),
        ];
    }
}
