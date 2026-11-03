<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\Writeup;
use Tests\FlagboardTestCase;

class WriteupTest extends FlagboardTestCase
{
    public function test_a_player_whose_team_solved_the_challenge_can_write_a_writeup(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition);
        Submission::factory()->create([
            'team_id' => $user->team_id, 'challenge_id' => $challenge->id,
            'user_id' => $user->id, 'is_correct' => true,
        ]);

        $this->actingAs($user)
            ->post(route('writeups.store', $challenge), [
                'title' => 'How I solved it',
                'content' => 'Step one, step two.',
                'status' => 'published',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('writeups', [
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'status' => 'published',
        ]);
    }

    public function test_a_player_whose_team_did_not_solve_the_challenge_cannot_write(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition);

        $this->actingAs($user)
            ->post(route('writeups.store', $challenge), [
                'title' => 'Spoiler',
                'content' => 'Nothing',
                'status' => 'published',
            ])
            ->assertSessionHas('error');

        $this->assertSame(0, Writeup::count());
    }

    public function test_a_draft_is_visible_to_its_author_only(): void
    {
        $author = $this->makePlayer();
        $other = $this->makePlayer();
        $writeup = Writeup::factory()->create(['user_id' => $author->id, 'status' => 'draft']);

        $this->actingAs($author)->get(route('writeups.show', $writeup))->assertOk();
        $this->actingAs($other)->get(route('writeups.show', $writeup))->assertNotFound();
    }

    public function test_a_published_writeup_is_visible_to_guests(): void
    {
        $writeup = Writeup::factory()->create(['status' => 'published', 'title' => 'Public writeup']);

        $this->get(route('writeups.show', $writeup))->assertOk()->assertSee('Public writeup');
        $this->get(route('writeups.index'))->assertOk()->assertSee('Public writeup');
    }

    public function test_only_the_author_can_edit_or_delete(): void
    {
        $author = $this->makePlayer();
        $other = $this->makePlayer();
        $writeup = Writeup::factory()->create(['user_id' => $author->id, 'status' => 'published']);

        $this->actingAs($other)->get(route('writeups.edit', $writeup))->assertForbidden();
        $this->actingAs($other)->delete(route('writeups.destroy', $writeup))->assertForbidden();
        $this->assertDatabaseHas('writeups', ['id' => $writeup->id]);

        $this->actingAs($author)
            ->put(route('writeups.update', $writeup), [
                'title' => 'Edited title',
                'content' => 'Edited content',
                'status' => 'published',
            ])
            ->assertRedirect(route('writeups.show', $writeup));
        $this->assertSame('Edited title', $writeup->fresh()->title);

        $this->actingAs($author)->delete(route('writeups.destroy', $writeup))->assertRedirect(route('writeups.index'));
        $this->assertDatabaseMissing('writeups', ['id' => $writeup->id]);
    }
}
