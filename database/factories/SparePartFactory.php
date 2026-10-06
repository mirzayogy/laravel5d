<?php

namespace Database\Factories;

use App\Models\SparePart;
use Illuminate\Database\Eloquent\Factories\Factory;

class SparePartFactory extends Factory
{
    protected $model = SparePart::class;

    public function definition(): array
    {
        return [
            'part_code' => 'PRT-' . fake()->unique()->numerify('#####'),
            'name' => fake()->randomElement([
                'Filter Oli Mesin',
                'Busi Iridium',
                'Kampas Rem Depan',
                'Kampas Rem Belakang',
                'Minyak Rem DOT 4',
                'Oli Mesin 5W-30 Fully Synthetic',
                'Air Radiator Coolant',
                'V-Belt Fan Belt',
                'Filter Udara Mesin',
                'Aki Kering 45Ah',
            ]),
            'brand' => fake()->randomElement(['Denso', 'Bosch', 'Shell', 'Motul', 'Aisin', 'Brembo', 'GS Astra']),
            'unit_price' => fake()->randomFloat(2, 45000, 950000),
            'stock_quantity' => fake()->numberBetween(10, 100),
            'compatibility' => fake()->randomElement(['Universal', 'Toyota & Daihatsu', 'Honda', 'Mitsubishi', 'Suzuki']),
        ];
    }
}
