<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nik' => $this->faker->numerify('6371##############'),
            'emergency_contact' => $this->faker->phoneNumber(),
            'notes' => $this->faker->sentence(),
            'avatar_url' => 'https://ui-avatars.com/api/?name=' . urlencode($this->faker->name()),
        ];
    }
}
