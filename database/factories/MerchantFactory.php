<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MerchantFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company() . ' Kitchen';
        return [
            'user_id' => User::factory(),
            'store_name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 999),
            'description' => fake()->paragraph(),
            'address' => fake()->streetAddress() . ', Banjarmasin',
            'phone_number' => fake()->phoneNumber(),
            'banner_image' => null,
            'is_open' => true,
            'rating' => fake()->randomFloat(2, 4.0, 5.0),
        ];
    }
}
