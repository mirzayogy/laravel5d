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
            'code' => 'PROMO' . strtoupper(fake()->unique()->lexify('????')),
            'discount_percentage' => fake()->randomElement([10.00, 15.00, 20.00, 25.00, 50.00]),
            'valid_until' => now()->addMonth()->format('Y-m-d'),
        ];
    }
}
