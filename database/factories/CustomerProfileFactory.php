<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone_number' => fake()->phoneNumber(),
            'avatar' => null,
            'address' => fake()->address(),
            'city' => 'Banjarmasin',
            'postal_code' => fake()->postcode(),
            'delivery_notes' => fake()->optional()->sentence(),
        ];
    }
}
