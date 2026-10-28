<?php

namespace Tests;

use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

abstract class FlagboardTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * A player that already belongs to a team.
     */
    protected function makePlayer(?Team $team = null): User
    {
        $team ??= Team::factory()->create();

        return User::factory()->create(['role' => 'player', 'team_id' => $team->id]);
    }

    protected function makeCompetition(string $status = 'running'): Competition
    {
        return Competition::factory()->create(['status' => $status]);
    }

    protected function register(Team $team, Competition $competition): void
    {
        $team->competitions()->attach($competition->id, ['registered_at' => now()]);
    }

    protected function makeChallenge(Competition $competition, string $flag = 'FLAG{test}', array $attributes = []): Challenge
    {
        return Challenge::factory()->create($attributes + [
            'competition_id' => $competition->id,
            'flag_hash' => Hash::make($flag),
            'is_published' => true,
        ]);
    }
}
