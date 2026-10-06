<?php

namespace Database\Factories;

use App\Models\Mechanic;
use Illuminate\Database\Eloquent\Factories\Factory;

class MechanicFactory extends Factory
{
    protected $model = Mechanic::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'specialization' => fake()->randomElement(['Mesin & Transmisi', 'Kelistrikan & AC', 'Kaki-kaki & Spooring', 'Perawatan Berkala']),
            'experience_years' => fake()->numberBetween(2, 15),
            'status' => fake()->randomElement(['Available', 'Busy', 'On Leave']),
        ];
    }
}
