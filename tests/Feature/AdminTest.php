<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Hint;
use Illuminate\Support\Facades\Hash;
use Tests\FlagboardTestCase;

class AdminTest extends FlagboardTestCase
{
    public function test_an_admin_can_create_a_competition(): void
    {
        $this->actingAs($this->makeAdmin())
            ->post(route('admin.competitions.store'), [
                'title' => 'Spring CTF',
                'description' => 'A test competition',
                'start_at' => '2026-11-01 09:00',
                'end_at' => '2026-11-02 09:00',
                'status' => 'draft',
            ])
            ->assertRedirect(route('admin.competitions.index'));

        $this->assertDatabaseHas('competitions', ['title' => 'Spring CTF', 'slug' => 'spring-ctf']);
    }

    public function test_a_competition_must_end_after_it_starts(): void
    {
        $this->actingAs($this->makeAdmin())
            ->post(route('admin.competitions.store'), [
                'title' => 'Broken CTF',
                'start_at' => '2026-11-02 09:00',
                'end_at' => '2026-11-01 09:00',
                'status' => 'draft',
            ])
            ->assertSessionHasErrors('end_at');

        $this->assertSame(0, Competition::count());
    }

    public function test_an_admin_can_create_a_challenge_with_a_hashed_flag_and_tags(): void
    {
        $competition = $this->makeCompetition();
        $category = Category::factory()->create();

        $this->actingAs($this->makeAdmin())
            ->post(route('admin.challenges.store'), [
                'competition_id' => $competition->id,
                'category_id' => $category->id,
                'title' => 'Login bypass',
                'description' => 'Get in without the password.',
                'points' => 200,
                'flag' => 'FLAG{new}',
                'difficulty' => 'medium',
                'is_published' => '1',
                'new_tags' => 'sql-injection, beginner',
            ])
            ->assertSessionHasNoErrors();

        $challenge = Challenge::where('title', 'Login bypass')->first();
        $this->assertNotNull($challenge);
        $this->assertTrue($challenge->is_published);
        $this->assertTrue(Hash::check('FLAG{new}', $challenge->flag_hash));
        $this->assertDatabaseMissing('challenges', ['flag_hash' => 'FLAG{new}']);
        $this->assertEqualsCanonicalizing(['sql-injection', 'beginner'], $challenge->tags->pluck('name')->all());
    }

    public function test_updating_a_challenge_without_a_flag_keeps_the_old_hash(): void
    {
        $competition = $this->makeCompetition();
        $challenge = $this->makeChallenge($competition, 'FLAG{keep}');
        $oldHash = $challenge->flag_hash;

        $this->actingAs($this->makeAdmin())
            ->put(route('admin.challenges.update', $challenge), [
                'competition_id' => $competition->id,
                'category_id' => $challenge->category_id,
                'title' => 'Renamed',
                'description' => $challenge->description,
                'points' => 150,
                'flag' => '',
                'difficulty' => 'hard',
            ])
            ->assertSessionHasNoErrors();

        $challenge->refresh();
        $this->assertSame('Renamed', $challenge->title);
        $this->assertSame($oldHash, $challenge->flag_hash);
        $this->assertFalse($challenge->is_published);
    }

    public function test_an_admin_can_add_and_delete_a_hint(): void
    {
        $challenge = $this->makeChallenge($this->makeCompetition());
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post(route('admin.hints.store', $challenge), ['content' => 'Try the robots file', 'cost' => 20])
            ->assertSessionHas('success');

        $hint = Hint::where('challenge_id', $challenge->id)->first();
        $this->assertSame(20, $hint->cost);

        $this->actingAs($admin)->delete(route('admin.hints.destroy', $hint))->assertSessionHas('success');
        $this->assertDatabaseMissing('hints', ['id' => $hint->id]);
    }

    public function test_a_player_cannot_create_a_challenge(): void
    {
        $competition = $this->makeCompetition();
        $category = Category::factory()->create();

        $this->actingAs($this->makePlayer())
            ->post(route('admin.challenges.store'), [
                'competition_id' => $competition->id,
                'category_id' => $category->id,
                'title' => 'Nope',
                'description' => 'Nope',
                'points' => 10,
                'flag' => 'FLAG{nope}',
                'difficulty' => 'easy',
            ])
            ->assertForbidden();

        $this->assertSame(0, Challenge::count());
    }
}
