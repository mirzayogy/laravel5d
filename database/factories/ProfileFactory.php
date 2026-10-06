<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone_number' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'identity_number' => fake()->numerify('63720###########'),
            'avatar' => null,
            'driver_license_type' => fake()->randomElement(['SIM A', 'SIM C', 'SIM A & C', 'None']),
        ];
    }
}
