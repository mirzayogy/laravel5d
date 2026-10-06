<?php

namespace Database\Factories;

use App\Models\Fish;
use App\Models\SalesLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesLog>
 */
class SalesLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'fish_id' => Fish::factory(),
            'logged_date' => fake()->date(),
            'value' => fake()->numberBetween(1, 20),
            'note' => fake()->sentence(),
        ];
    }
}
