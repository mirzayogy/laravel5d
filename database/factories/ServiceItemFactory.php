<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use App\Models\ServiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceItemFactory extends Factory
{
    protected $model = ServiceItem::class;

    public function definition(): array
    {
        return [
            'service_category_id' => ServiceCategory::factory(),
            'name' => $this->faker->words(2, true),
            'price_per_unit' => $this->faker->randomElement([7000, 10000, 15000, 25000, 35000]),
            'unit_type' => $this->faker->randomElement(['kg', 'pcs', 'pair']),
            'estimated_hours' => $this->faker->randomElement([6, 12, 24, 48]),
        ];
    }
}
