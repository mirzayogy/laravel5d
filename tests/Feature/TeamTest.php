<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Tests\FlagboardTestCase;

class TeamTest extends FlagboardTestCase
{
    public function test_a_user_can_create_a_team(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('team.store'), ['name' => 'Null Pointers'])
            ->assertRedirect(route('team.show'));

        $team = Team::where('name', 'Null Pointers')->first();
        $this->assertNotNull($team);
        $this->assertSame(8, strlen($team->invite_code));
        $this->assertSame($team->id, $user->fresh()->team_id);
    }

    public function test_a_user_in_a_team_cannot_create_another_one(): void
    {
        $user = $this->makePlayer();

        $this->actingAs($user)
            ->post(route('team.store'), ['name' => 'Second Team'])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('teams', ['name' => 'Second Team']);
    }

    public function test_a_user_can_join_with_an_invite_code(): void
    {
        $team = Team::factory()->create(['invite_code' => 'ABCD1234']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('team.join'), ['invite_code' => 'abcd1234'])
            ->assertRedirect(route('team.show'));

        $this->assertSame($team->id, $user->fresh()->team_id);
    }

    public function test_an_invalid_invite_code_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('team.join'), ['invite_code' => 'NOPE0000'])
            ->assertSessionHasErrors('invite_code');

        $this->assertNull($user->fresh()->team_id);
    }

    public function test_a_user_can_leave_the_team(): void
    {
        $user = $this->makePlayer();

        $this->actingAs($user)
            ->delete(route('team.leave'))
            ->assertRedirect(route('team.show'));

        $this->assertNull($user->fresh()->team_id);
    }
}
