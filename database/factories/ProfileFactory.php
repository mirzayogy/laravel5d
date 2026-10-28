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
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => fake()->sentence(),
            'institution' => fake()->company(),
            'github_url' => 'https://github.com/' . fake()->unique()->userName(),
        ];
    }
}
