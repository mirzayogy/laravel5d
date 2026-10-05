<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
        'name' => fake()->name('male'),
        'jersey_number' => fake()->numberBetween(1, 99),
        'position' => fake()->randomElement(['GK', 'DEF', 'MID', 'FWD']),
        ];
    }
}
