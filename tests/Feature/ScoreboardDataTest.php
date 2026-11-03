<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\Team;
use Tests\FlagboardTestCase;

class ScoreboardDataTest extends FlagboardTestCase
{
    public function test_the_json_feed_holds_the_ranking_and_the_chart_series(): void
    {
        $competition = $this->makeCompetition();
        $challenge = $this->makeChallenge($competition, 'FLAG{a}', ['points' => 200]);
        $team = Team::factory()->create(['name' => 'Chartists']);
        $this->register($team, $competition);
        Submission::factory()->create([
            'team_id' => $team->id, 'challenge_id' => $challenge->id, 'user_id' => $this->makePlayer($team)->id,
            'is_correct' => true, 'submitted_at' => now()->subMinutes(5),
        ]);

        $response = $this->getJson(route('scoreboard.data', $competition))->assertOk();

        $response->assertJsonPath('ranking.0.name', 'Chartists')
            ->assertJsonPath('ranking.0.score', 200)
            ->assertJsonPath('series.0.name', 'Chartists')
            ->assertJsonPath('phase', 'running');
        $points = $response->json('series.0.points');
        $this->assertSame(0, $points[0][1]);
        $this->assertSame(200, end($points)[1]);
    }

    public function test_a_draft_scoreboard_is_hidden_from_players_but_not_from_admins(): void
    {
        $competition = $this->makeCompetition('draft');

        $this->getJson(route('scoreboard.data', $competition))->assertNotFound();
        $this->actingAs($this->makeAdmin())->get(route('scoreboard.show', $competition))->assertOk();
    }
}
