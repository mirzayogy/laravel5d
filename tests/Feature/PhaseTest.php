<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Submission;
use Tests\FlagboardTestCase;

class PhaseTest extends FlagboardTestCase
{
    public function test_the_phase_follows_the_status_and_the_time(): void
    {
        $this->assertSame('draft', Competition::factory()->create(['status' => 'draft'])->phase);
        $this->assertSame('running', Competition::factory()->create()->phase);
        $this->assertSame('upcoming', Competition::factory()->create(['start_at' => now()->addDay(), 'end_at' => now()->addDays(2)])->phase);
        $this->assertSame('finished', Competition::factory()->create(['start_at' => now()->subDays(3), 'end_at' => now()->subDay()])->phase);
        $this->assertSame('finished', Competition::factory()->create(['status' => 'finished'])->phase);
    }

    private function upcoming(): Competition
    {
        return Competition::factory()->create(['start_at' => now()->addDay(), 'end_at' => now()->addDays(2)]);
    }

    public function test_a_team_can_register_for_an_upcoming_competition(): void
    {
        $competition = $this->upcoming();
        $user = $this->makePlayer();

        $this->actingAs($user)
            ->post(route('competitions.register', $competition))
            ->assertSessionHas('success');

        $this->assertSame(1, $competition->teams()->count());
    }

    public function test_the_challenges_stay_locked_until_the_start(): void
    {
        $competition = $this->upcoming();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition);

        $this->actingAs($user)
            ->get(route('challenges.index', $competition))
            ->assertRedirect(route('competitions.show', $competition));

        $this->actingAs($user)
            ->get(route('challenges.show', $challenge))
            ->assertRedirect(route('competitions.show', $competition));
    }

    public function test_submissions_are_closed_before_the_start(): void
    {
        $competition = $this->upcoming();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition, 'FLAG{early}');

        $this->actingAs($user)
            ->post(route('challenges.submit', $challenge), ['flag' => 'FLAG{early}'])
            ->assertSessionHas('error');

        $this->assertSame(0, Submission::count());
    }

    public function test_an_admin_can_preview_the_challenges_of_a_draft_competition(): void
    {
        $competition = $this->makeCompetition('draft');
        $challenge = $this->makeChallenge($competition);

        $this->actingAs($this->makePlayer())->get(route('competitions.show', $competition))->assertNotFound();

        $admin = $this->makeAdmin();
        $this->actingAs($admin)->get(route('competitions.show', $competition))->assertOk();
        $this->actingAs($admin)->get(route('challenges.show', $challenge))->assertOk();
    }
}
