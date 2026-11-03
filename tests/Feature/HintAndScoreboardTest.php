<?php

namespace Tests\Feature;

use App\Models\Hint;
use App\Models\Submission;
use App\Models\Team;
use App\Services\ContestService;
use Tests\FlagboardTestCase;

class HintAndScoreboardTest extends FlagboardTestCase
{
    public function test_a_team_can_unlock_a_hint_once(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition);
        $hint = Hint::factory()->create(['challenge_id' => $challenge->id, 'cost' => 25, 'content' => 'Look at the cookies']);

        $this->actingAs($user)
            ->post(route('hints.unlock', $hint))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('hint_unlocks', ['team_id' => $user->team_id, 'hint_id' => $hint->id]);

        $this->actingAs($user)
            ->post(route('hints.unlock', $hint))
            ->assertSessionHas('error');

        $this->assertSame(1, $user->team->hints()->count());

        $this->actingAs($user)
            ->get(route('challenges.show', $challenge))
            ->assertSee('Look at the cookies');
    }

    public function test_a_locked_hint_is_not_shown(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition);
        Hint::factory()->create(['challenge_id' => $challenge->id, 'content' => 'Secret hint text']);

        $this->actingAs($user)
            ->get(route('challenges.show', $challenge))
            ->assertDontSee('Secret hint text');
    }

    public function test_a_hint_cannot_be_unlocked_after_the_challenge_is_solved(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition, 'FLAG{secret}');
        $hint = Hint::factory()->create(['challenge_id' => $challenge->id]);

        $this->actingAs($user)->post(route('challenges.submit', $challenge), ['flag' => 'FLAG{secret}']);

        $this->actingAs($user)
            ->post(route('hints.unlock', $hint))
            ->assertSessionHas('error');

        $this->assertSame(0, $user->team->hints()->count());
    }

    public function test_the_score_is_points_minus_hint_costs(): void
    {
        $competition = $this->makeCompetition();
        $big = $this->makeChallenge($competition, 'FLAG{big}', ['points' => 300]);
        $small = $this->makeChallenge($competition, 'FLAG{small}', ['points' => 100]);
        $hint = Hint::factory()->create(['challenge_id' => $small->id, 'cost' => 50]);

        $alpha = Team::factory()->create(['name' => 'Alpha']);
        $beta = Team::factory()->create(['name' => 'Beta']);
        $this->register($alpha, $competition);
        $this->register($beta, $competition);

        $alphaUser = $this->makePlayer($alpha);
        $betaUser = $this->makePlayer($beta);

        Submission::factory()->create([
            'team_id' => $alpha->id, 'challenge_id' => $big->id, 'user_id' => $alphaUser->id,
            'is_correct' => true, 'submitted_at' => now()->subMinutes(10),
        ]);
        Submission::factory()->create([
            'team_id' => $beta->id, 'challenge_id' => $small->id, 'user_id' => $betaUser->id,
            'is_correct' => true, 'submitted_at' => now()->subMinutes(5),
        ]);
        Submission::factory()->create([
            'team_id' => $beta->id, 'challenge_id' => $big->id, 'user_id' => $betaUser->id,
            'is_correct' => false, 'submitted_at' => now()->subMinutes(4),
        ]);
        $beta->hints()->attach($hint->id, ['unlocked_at' => now()]);

        $ranking = app(ContestService::class)->ranking($competition);

        $this->assertSame(['Alpha', 'Beta'], $ranking->pluck('team.name')->all());
        $this->assertSame(300, $ranking[0]->score);
        $this->assertSame(50, $ranking[1]->score);
        $this->assertSame(100, $ranking[1]->points);
        $this->assertSame(50, $ranking[1]->hint_cost);
        $this->assertSame(1, $ranking[1]->solves);
    }

    public function test_a_tie_is_broken_by_the_earlier_last_solve(): void
    {
        $competition = $this->makeCompetition();
        $challenge = $this->makeChallenge($competition, 'FLAG{x}', ['points' => 100]);

        $slow = Team::factory()->create(['name' => 'Aardvarks']);
        $fast = Team::factory()->create(['name' => 'Zebras']);
        $this->register($slow, $competition);
        $this->register($fast, $competition);

        Submission::factory()->create([
            'team_id' => $slow->id, 'challenge_id' => $challenge->id, 'user_id' => $this->makePlayer($slow)->id,
            'is_correct' => true, 'submitted_at' => now()->subMinutes(1),
        ]);
        Submission::factory()->create([
            'team_id' => $fast->id, 'challenge_id' => $challenge->id, 'user_id' => $this->makePlayer($fast)->id,
            'is_correct' => true, 'submitted_at' => now()->subMinutes(30),
        ]);

        $ranking = app(ContestService::class)->ranking($competition);

        $this->assertSame(['Zebras', 'Aardvarks'], $ranking->pluck('team.name')->all());
    }

    public function test_the_scoreboard_page_is_public(): void
    {
        $competition = $this->makeCompetition();
        $team = Team::factory()->create(['name' => 'Visible Team']);
        $this->register($team, $competition);

        $this->get(route('scoreboard.show', $competition))
            ->assertOk()
            ->assertSee('Visible Team');
    }
}
