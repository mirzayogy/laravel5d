<?php

namespace Tests\Feature;

use App\Models\Submission;
use Tests\FlagboardTestCase;

class FlagSubmissionTest extends FlagboardTestCase
{
    public function test_the_challenge_list_needs_a_registered_team(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();

        $this->actingAs($user)
            ->get(route('challenges.index', $competition))
            ->assertRedirect(route('competitions.show', $competition));
    }

    public function test_a_registered_team_sees_published_challenges_only(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);

        $visible = $this->makeChallenge($competition);
        $hidden = $this->makeChallenge($competition, 'FLAG{hidden}', ['is_published' => false]);

        $this->actingAs($user)
            ->get(route('challenges.index', $competition))
            ->assertOk()
            ->assertSee($visible->title)
            ->assertDontSee($hidden->title);

        $this->actingAs($user)
            ->get(route('challenges.show', $hidden))
            ->assertNotFound();
    }

    public function test_a_correct_flag_is_recorded(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition, 'FLAG{secret}');

        $this->actingAs($user)
            ->post(route('challenges.submit', $challenge), ['flag' => '  FLAG{secret}  '])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('submissions', [
            'team_id' => $user->team_id,
            'challenge_id' => $challenge->id,
            'user_id' => $user->id,
            'is_correct' => true,
        ]);
    }

    public function test_a_wrong_flag_is_recorded_as_incorrect(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition, 'FLAG{secret}');

        $this->actingAs($user)
            ->post(route('challenges.submit', $challenge), ['flag' => 'FLAG{guess}'])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('submissions', [
            'team_id' => $user->team_id,
            'challenge_id' => $challenge->id,
            'is_correct' => false,
        ]);
    }

    public function test_a_team_cannot_solve_the_same_challenge_twice(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $teammate = $this->makePlayer($user->team);
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition, 'FLAG{secret}');

        $this->actingAs($user)->post(route('challenges.submit', $challenge), ['flag' => 'FLAG{secret}']);
        $this->actingAs($teammate)
            ->post(route('challenges.submit', $challenge), ['flag' => 'FLAG{secret}'])
            ->assertSessionHas('error');

        $this->assertSame(1, Submission::where('challenge_id', $challenge->id)->where('is_correct', true)->count());
    }

    public function test_a_team_that_is_not_registered_cannot_submit(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $challenge = $this->makeChallenge($competition, 'FLAG{secret}');

        $this->actingAs($user)
            ->post(route('challenges.submit', $challenge), ['flag' => 'FLAG{secret}'])
            ->assertSessionHas('error');

        $this->assertSame(0, Submission::count());
    }

    public function test_submissions_are_closed_when_the_competition_is_not_running(): void
    {
        $competition = $this->makeCompetition('finished');
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition, 'FLAG{secret}');

        $this->actingAs($user)
            ->post(route('challenges.submit', $challenge), ['flag' => 'FLAG{secret}'])
            ->assertSessionHas('error');

        $this->assertSame(0, Submission::count());
    }

    public function test_the_flag_hash_is_never_shown(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition, 'FLAG{secret}');

        $this->actingAs($user)
            ->get(route('challenges.show', $challenge))
            ->assertOk()
            ->assertDontSee($challenge->flag_hash)
            ->assertDontSee('FLAG{secret}');
    }
}
