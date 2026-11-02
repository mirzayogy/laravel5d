<?php

namespace Database\Factories;

use App\Models\GroomerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroomerProfileFactory extends Factory
{
    protected $model = GroomerProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role' => 'groomer',
            ]),
            'specialization' => fake()->randomElement([
                'Cat Grooming',
                'Dog Grooming',
                'Full Grooming',
            ]),
            'experience_years' => fake()->numberBetween(1, 8),
            'bio' => fake()->sentence(),
            'is_available' => true,
        ];
    }
}