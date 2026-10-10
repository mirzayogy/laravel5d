<?php

namespace Database\Factories;

use App\Models\BookDetail;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookDetailFactory extends Factory
{
    protected $model = BookDetail::class;

    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
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
