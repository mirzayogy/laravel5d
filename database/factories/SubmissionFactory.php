<?php

namespace Database\Factories;

use App\Models\Challenge;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'challenge_id' => Challenge::factory(),
            'user_id' => User::factory(),
            'is_correct' => fake()->boolean(),
            'submitted_at' => now(),
        ];
    }
}
