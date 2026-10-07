<?php

namespace Database\Factories;

use App\Models\StorageRack;
use Illuminate\Database\Eloquent\Factories\Factory;

class StorageRackFactory extends Factory
{
    protected $model = StorageRack::class;

    public function definition(): array
    {
        return [
            'code' => 'RAK-' . strtoupper($this->faker->bothify('?#')),
            'section' => $this->faker->randomElement(['Zona A (Kiloan)', 'Zona B (Express)', 'Zona C (Sepatu/Tas)']),
            'capacity' => 15,
        ];
    }
}
