<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition(): array
    {
        return [
            'promo_code' => strtoupper(fake()->unique()->bothify('PROMO-###??')),
            'title' => fake()->sentence(3),
            'discount_percentage' => fake()->randomFloat(2, 5, 25),
            'max_discount_amount' => fake()->randomFloat(2, 50000, 200000),
            'min_transaction_amount' => fake()->randomFloat(2, 100000, 300000),
            'valid_until' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'is_active' => true,
        ];
    }
}
