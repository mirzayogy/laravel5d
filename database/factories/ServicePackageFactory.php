<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use App\Models\ServicePackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicePackageFactory extends Factory
{
    protected $model = ServicePackage::class;

    public function definition(): array
    {
        return [
            'service_category_id' => ServiceCategory::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'base_price' => fake()->randomFloat(2, 100000, 1500000),
            'estimated_duration_minutes' => fake()->randomElement([30, 45, 60, 90, 120, 180]),
        ];
    }
}
