<?php

namespace Database\Factories;

use App\Models\Organizer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organizer>
 */
class OrganizerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Perkumpulan Kicau Mania Wilayah '.fake()->unique()->numberBetween(1, 9999),
            'city' => fake()->randomElement([
                'Banjarbaru', 'Banjarmasin', 'Martapura', 'Palangka Raya', 'Pangkalan Bun',
                'Samarinda', 'Balikpapan', 'Pontianak', 'Tanjung', 'Amuntai', 'Kandangan', 'Sampit',
            ]),
            'phone' => fake()->numerify('08##########'),
            'email' => fake()->optional(0.7)->safeEmail(),
        ];
    }
}
