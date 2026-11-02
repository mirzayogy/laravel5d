<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition(): array
    {
        $species = fake()->randomElement(['cat', 'dog']);

        return [
            'user_id' => User::factory()->state([
                'role' => 'customer',
            ]),
            'name' => fake()->firstName(),
            'species' => $species,
            'breed' => $species === 'cat'
                ? fake()->randomElement(['Persian', 'Anggora', 'Domestic'])
                : fake()->randomElement(['Poodle', 'Golden Retriever', 'Husky']),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_date' => fake()->dateTimeBetween('-8 years', '-6 months'),
            'weight' => fake()->randomFloat(2, 1, 30),
            'color' => fake()->randomElement(['White', 'Black', 'Brown', 'Gray']),
            'photo' => null,
            'special_notes' => fake()->optional()->sentence(),
            'health_notes' => fake()->optional()->sentence(),
        ];
    }
}
