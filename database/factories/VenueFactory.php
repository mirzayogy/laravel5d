<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venue>
 */
class VenueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'name' => fake()->company() . ' Minisoccer',
        'city' => fake()->city(),
        'address' => fake()->streetAddress(),
        'phone' => fake()->phoneNumber(),
        'description' => fake()->sentence(),
        ];
    }
}
