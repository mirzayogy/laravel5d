<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceCategoryFactory extends Factory
{
    protected $model = ServiceCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Kiloan Regular',
            'Kiloan Express',
            'Dry Cleaning',
            'Cuci Sepatu & Tas',
            'Setrika saja',
        ]);

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => $this->faker->sentence(),
        ];
    }
}
