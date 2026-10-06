<?php

namespace Database\Factories;

use App\Models\Bird;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bird>
 */
class BirdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'species_id' => Species::factory(),
            'sire_id' => null,
            'dam_id' => null,
            'name' => fake()->randomElement([
                'Petir', 'Bima', 'Rajawali', 'Macan', 'Gatotkaca', 'Srikandi',
                'Jagoan', 'Pangeran', 'Badai', 'Tornado', 'Mutiara', 'Cakra',
            ]),
            'ring_number' => strtoupper(fake()->unique()->bothify('GTG-####-??')),
            'sex' => fake()->randomElement(['male', 'female', 'unknown']),
            'hatched_on' => fake()->dateTimeBetween('-5 years', '-6 months'),
            'color' => fake()->randomElement(['Black', 'Brown', 'Green', 'Yellow', 'Grey', 'Mixed']),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the bird is male.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => ['sex' => 'male']);
    }

    /**
     * Indicate that the bird is female.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => ['sex' => 'female']);
    }
}
