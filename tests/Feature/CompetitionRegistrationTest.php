<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\FlagboardTestCase;

class CompetitionRegistrationTest extends FlagboardTestCase
{
    public function test_the_home_page_lists_running_competitions_only(): void
    {
        $running = $this->makeCompetition('running');
        $finished = $this->makeCompetition('finished');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($running->title)
            ->assertDontSee($finished->title);
    }

    public function test_a_draft_competition_is_hidden(): void
    {
        $draft = $this->makeCompetition('draft');

        $this->get(route('competitions.show', $draft))->assertNotFound();
    }

    public function test_a_team_can_register_for_a_running_competition(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();

        $this->actingAs($user)
            ->post(route('competitions.register', $competition))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('competition_team', [
            'competition_id' => $competition->id,
            'team_id' => $user->team_id,
        ]);
    }

    public function test_a_team_cannot_register_twice(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);

        $this->actingAs($user)
            ->post(route('competitions.register', $competition))
            ->assertSessionHas('error');

        $this->assertSame(1, $competition->teams()->count());
    }

    public function test_a_user_without_a_team_cannot_register(): void
    {
        $competition = $this->makeCompetition();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('competitions.register', $competition))
            ->assertSessionHas('error');

        $this->assertSame(0, $competition->teams()->count());
    }

    public function test_registration_is_closed_for_a_finished_competition(): void
    {
        $competition = $this->makeCompetition('finished');
        $user = $this->makePlayer();

        $this->actingAs($user)
            ->post(route('competitions.register', $competition))
            ->assertSessionHas('error');

        $this->assertSame(0, $competition->teams()->count());
    }
}
