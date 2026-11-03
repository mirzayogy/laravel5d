<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Submission;
use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use Tests\FlagboardTestCase;

class AdminManagementTest extends FlagboardTestCase
{
    public function test_an_admin_can_change_a_role_and_the_user_is_told(): void
    {
        $player = $this->makePlayer();

        $this->actingAs($this->makeAdmin())
            ->patch(route('admin.users.role', $player), ['role' => 'admin'])
            ->assertSessionHas('success');

        $this->assertSame('admin', $player->fresh()->role);
        $this->assertSame('Your role changed', $player->notifications()->first()->data['title']);
    }

    public function test_an_admin_cannot_change_or_delete_their_own_account(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->patch(route('admin.users.role', $admin), ['role' => 'player'])->assertSessionHas('error');
        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertSessionHas('error');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_a_user_with_submissions_cannot_be_deleted(): void
    {
        $player = $this->makePlayer();
        $challenge = $this->makeChallenge($this->makeCompetition());
        Submission::factory()->create(['team_id' => $player->team_id, 'challenge_id' => $challenge->id, 'user_id' => $player->id]);
        $spare = $this->makePlayer();

        $admin = $this->makeAdmin();
        $this->actingAs($admin)->delete(route('admin.users.destroy', $player))->assertSessionHas('error');
        $this->actingAs($admin)->delete(route('admin.users.destroy', $spare))->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $player->id]);
        $this->assertDatabaseMissing('users', ['id' => $spare->id]);
    }

    public function test_an_admin_can_verify_an_email(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($this->makeAdmin())->post(route('admin.users.verify', $user))->assertSessionHas('success');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_the_user_list_can_be_filtered(): void
    {
        User::factory()->create(['name' => 'Findable Person', 'role' => 'player']);
        User::factory()->create(['name' => 'Someone Else', 'role' => 'player']);

        $this->actingAs($this->makeAdmin())
            ->get(route('admin.users.index', ['q' => 'Findable']))
            ->assertOk()
            ->assertSee('Findable Person')
            ->assertDontSee('Someone Else');
    }

    public function test_a_team_with_submissions_cannot_be_deleted_by_an_admin(): void
    {
        $player = $this->makePlayer();
        $challenge = $this->makeChallenge($this->makeCompetition());
        Submission::factory()->create(['team_id' => $player->team_id, 'challenge_id' => $challenge->id, 'user_id' => $player->id]);
        $empty = Team::factory()->create();
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->delete(route('admin.teams.destroy', $player->team))->assertSessionHas('error');
        $this->actingAs($admin)->delete(route('admin.teams.destroy', $empty))->assertSessionHas('success');

        $this->assertDatabaseHas('teams', ['id' => $player->team_id]);
        $this->assertDatabaseMissing('teams', ['id' => $empty->id]);
    }

    public function test_the_challenge_list_can_be_filtered_by_competition(): void
    {
        $first = $this->makeCompetition();
        $second = $this->makeCompetition();
        $mine = $this->makeChallenge($first, 'FLAG{1}', ['title' => 'Visible Challenge']);
        $this->makeChallenge($second, 'FLAG{2}', ['title' => 'Hidden Challenge']);

        $this->actingAs($this->makeAdmin())
            ->get(route('admin.challenges.index', ['competition' => $first->id]))
            ->assertOk()
            ->assertSee($mine->title)
            ->assertDontSee('Hidden Challenge');
    }

    public function test_categories_and_tags_can_be_managed(): void
    {
        $admin = $this->makeAdmin();
        $category = Category::factory()->create();
        $this->makeChallenge($this->makeCompetition(), 'FLAG{c}', ['category_id' => $category->id]);

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category))->assertSessionHas('error');
        $this->actingAs($admin)->post(route('admin.tags.store'), ['name' => 'forensics'])->assertSessionHas('success');

        $tag = Tag::where('name', 'forensics')->first();
        $this->actingAs($admin)->put(route('admin.tags.update', $tag), ['name' => 'steganography'])->assertSessionHas('success');
        $this->actingAs($admin)->delete(route('admin.tags.destroy', $tag))->assertSessionHas('success');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_the_admin_pages_render(): void
    {
        $admin = $this->makeAdmin();
        $challenge = $this->makeChallenge($this->makeCompetition());

        foreach ([
            route('admin.dashboard'),
            route('admin.competitions.index'),
            route('admin.competitions.create'),
            route('admin.challenges.index'),
            route('admin.challenges.create'),
            route('admin.challenges.edit', $challenge),
            route('admin.users.index'),
            route('admin.teams.index'),
            route('admin.categories.index'),
            route('admin.tags.index'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }
}
