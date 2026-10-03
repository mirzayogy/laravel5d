<?php

namespace Database\Factories;

use App\Models\Symptom;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Symptom>
 */
class SymptomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
            'category' => fake()->randomElement(['physical', 'skin', 'digestive', 'sleep']),
            'icon' => null,
        ];
    }
}
