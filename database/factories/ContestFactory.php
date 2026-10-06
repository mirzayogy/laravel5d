<?php

namespace Database\Factories;

use App\Models\Contest;
use App\Models\Judge;
use App\Models\Organizer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contest>
 */
class ContestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = fake()->randomElement(['Banjarbaru', 'Banjarmasin', 'Palangka Raya', 'Samarinda', 'Pontianak']);

        return [
            'organizer_id' => Organizer::factory(),
            'chief_judge_id' => Judge::factory(),
            'name' => fake()->randomElement(['Piala Walikota', 'Piala Gubernur', 'Latber Akbar', 'Kapolres Cup', 'Borneo Open']).' '.$city,
            'venue' => fake()->randomElement(['Lapangan Murjani', 'Gedung Olahraga', 'Taman Kota', 'Halaman Balai Kota']),
            'city' => $city,
            'held_on' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'registration_fee' => fake()->randomElement([50000, 75000, 100000, 150000]),
            'status' => 'upcoming',
        ];
    }

    /**
     * Indicate that the contest has already been held.
     */
    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'held_on' => fake()->dateTimeBetween('-6 months', '-1 week'),
            'status' => 'finished',
        ]);
    }

    /**
     * Indicate that the contest is being held today.
     */
    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'held_on' => now()->toDateString(),
            'status' => 'ongoing',
        ]);
    }

    /**
     * Indicate that the contest has not started yet.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'held_on' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'status' => 'upcoming',
        ]);
    }
}
