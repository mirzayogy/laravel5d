<?php

namespace Database\Factories;

use App\Models\ItemSpecification;
use App\Models\RentalItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemSpecificationFactory extends Factory
{
    protected $model = ItemSpecification::class;

    public function definition(): array
    {
        return [
            'rental_item_id' => RentalItem::factory(),
            'serial_or_plate_number' => strtoupper(fake()->bothify('??-####-??')),
            'year_of_manufacture' => fake()->numberBetween(2018, 2025),
            'color' => fake()->safeColorName(),
            'condition_notes' => 'Unit dalam kondisi prima dan siap sewa.',
            'technical_specs' => [
                'power' => 'Standard',
                'warranty' => 'Active',
                'maintenance_cycle' => 'Monthly',
            ],
        ];
    }
}
