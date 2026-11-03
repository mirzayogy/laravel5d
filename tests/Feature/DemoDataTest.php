<?php

namespace Tests\Feature;

use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use App\Models\Writeup;
use App\Services\ContestService;
use Tests\FlagboardTestCase;

class DemoDataTest extends FlagboardTestCase
{
    public function test_the_seeder_creates_a_full_demo_world(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', ['email' => 'admin@example.com', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'player@example.com', 'role' => 'player']);
        $this->assertDatabaseHas('users', ['email' => 'unverified@example.com', 'email_verified_at' => null]);
        $this->assertSame(14, Team::count());
        $this->assertGreaterThanOrEqual(50, Challenge::count());
        $this->assertGreaterThan(300, Submission::count());

        $phases = Competition::all()->pluck('phase')->sort()->values()->all();
        $this->assertSame(['draft', 'finished', 'finished', 'running', 'upcoming'], $phases);

        $captain = User::where('email', 'player@example.com')->first();
        $this->assertTrue($captain->team->isCaptain($captain));

        $running = Competition::where('title', 'Flagboard Spring Open')->first();
        $this->assertGreaterThan(5, app(ContestService::class)->ranking($running)->count());
    }

    public function test_every_main_page_renders_with_the_demo_data(): void
    {
        $this->seed();
        $writeup = Writeup::where('status', 'published')->first();

        foreach (['/', '/competitions', '/competitions/1', '/competitions/2', '/competitions/3', '/competitions/1/scoreboard', '/competitions/2/scoreboard', '/teams', '/teams/1', '/writeups', '/writeups/'.$writeup->id, '/login', '/register', '/forgot-password'] as $url) {
            $this->get($url)->assertOk();
        }

        $player = User::where('email', 'player@example.com')->first();

        foreach (['/dashboard', '/team', '/profile', '/notifications', '/competitions/1/challenges', '/challenges/1', '/writeups?mine=1', '/competitions/3'] as $url) {
            $this->actingAs($player)->get($url)->assertOk();
        }

        $solo = User::where('email', 'solo@example.com')->first();
        $this->actingAs($solo)->get('/dashboard')->assertOk();
        $this->actingAs($solo)->get('/team')->assertOk();

        $admin = User::where('email', 'admin@example.com')->first();
        foreach (['/admin', '/admin/competitions', '/admin/competitions/1/edit', '/admin/challenges', '/admin/challenges/1/edit', '/admin/users', '/admin/teams', '/admin/categories', '/admin/tags'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->get('/does-not-exist')->assertNotFound();
    }
}
