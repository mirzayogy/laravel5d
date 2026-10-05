<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Rumah', 'Kantor', 'Kos']),
            'recipient_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'address_line' => fake()->streetAddress(),
            'city' => fake()->city(),
            'province' => fake()->randomElement(['Kalimantan Selatan', 'Jawa Barat', 'Jawa Timur', 'DKI Jakarta']),
            'postal_code' => fake()->postcode(),
            'is_default' => false,
        ];
    }
}