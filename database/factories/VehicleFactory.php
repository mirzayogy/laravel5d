<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        $brands = [
            'Toyota' => ['Avanza', 'Innova', 'Fortuner', 'Yaris'],
            'Honda' => ['Civic', 'HR-V', 'CR-V', 'Brio'],
            'Mitsubishi' => ['Pajero Sport', 'Xpander', 'Outlander'],
            'Suzuki' => ['Ertiga', 'Jimny', 'Baleno'],
            'Daihatsu' => ['Xenia', 'Terios', 'Rocky'],
        ];

        $brand = fake()->randomElement(array_keys($brands));
        $model = fake()->randomElement($brands[$brand]);

        return [
            'user_id' => User::factory(),
            'plate_number' => 'DA ' . fake()->numberBetween(1000, 9999) . ' ' . fake()->regexify('[A-Z]{2,3}'),
            'brand' => $brand,
            'model' => $model,
            'production_year' => fake()->numberBetween(2015, 2024),
            'color' => fake()->randomElement(['Hitam', 'Putih', 'Abu-abu', 'Perak', 'Merah']),
            'transmission_type' => fake()->randomElement(['Manual', 'Automatic', 'CVT']),
        ];
    }
}
