<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $location = fake()->randomElement([
            ['city' => 'Banjarbaru', 'province' => 'Kalimantan Selatan'],
            ['city' => 'Banjarmasin', 'province' => 'Kalimantan Selatan'],
            ['city' => 'Martapura', 'province' => 'Kalimantan Selatan'],
            ['city' => 'Palangka Raya', 'province' => 'Kalimantan Tengah'],
            ['city' => 'Pangkalan Bun', 'province' => 'Kalimantan Tengah'],
            ['city' => 'Samarinda', 'province' => 'Kalimantan Timur'],
            ['city' => 'Balikpapan', 'province' => 'Kalimantan Timur'],
            ['city' => 'Pontianak', 'province' => 'Kalimantan Barat'],
        ]);

        return [
            'user_id' => User::factory(),
            'phone' => fake()->numerify('08##########'),
            'city' => $location['city'],
            'province' => $location['province'],
            'club_name' => fake()->optional(0.7)->randomElement([
                'Kicau Mania',
                'Gantangan Borneo',
                'Pecinta Murai Batu',
                'Sahabat Kenari',
                'Komunitas Kacer',
            ]),
            'bio' => fake()->optional(0.6)->sentence(),
        ];
    }
}
