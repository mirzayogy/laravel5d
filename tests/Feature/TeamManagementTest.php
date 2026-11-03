<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Tests\FlagboardTestCase;

class TeamManagementTest extends FlagboardTestCase
{
    /** @return array{User, User, Team} */
    private function teamWithCaptain(): array
    {
        $captain = User::factory()->create();
        $team = Team::factory()->create(['captain_id' => $captain->id]);
        $captain->update(['team_id' => $team->id]);
        $member = $this->makePlayer($team);

        return [$captain, $member, $team];
    }

    public function test_the_creator_becomes_the_captain(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('team.store'), ['name' => 'Captains Club']);

        $team = Team::where('name', 'Captains Club')->first();
        $this->assertSame($user->id, $team->captain_id);
    }

    public function test_a_full_team_cannot_be_joined(): void
    {
        config(['flagboard.team_size' => 2]);
        [, , $team] = $this->teamWithCaptain();
        $newcomer = User::factory()->create();

        $this->actingAs($newcomer)
            ->post(route('team.join'), ['invite_code' => $team->invite_code])
            ->assertSessionHasErrors('invite_code');

        $this->assertNull($newcomer->fresh()->team_id);
    }

    public function test_the_teammates_are_notified_when_someone_joins(): void
    {
        [$captain, , $team] = $this->teamWithCaptain();
        $newcomer = User::factory()->create(['name' => 'Newcomer']);

        $this->actingAs($newcomer)->post(route('team.join'), ['invite_code' => $team->invite_code]);

        $this->assertSame('New teammate', $captain->notifications()->first()->data['title']);
    }

    public function test_the_captain_can_remove_a_member(): void
    {
        [$captain, $member] = $this->teamWithCaptain();

        $this->actingAs($captain)->delete(route('team.members.remove', $member))->assertSessionHas('success');

        $this->assertNull($member->fresh()->team_id);
        $this->assertSame(1, $member->notifications()->count());
    }

    public function test_only_the_captain_can_manage_the_team(): void
    {
        [, $member, $team] = $this->teamWithCaptain();
        $other = $this->makePlayer($team);

        $this->actingAs($member)->delete(route('team.members.remove', $other))->assertForbidden();
        $this->actingAs($member)->post(route('team.captain', $other))->assertForbidden();
        $this->actingAs($member)->post(route('team.code'))->assertForbidden();
        $this->actingAs($member)->put(route('team.update'), ['name' => 'Hijacked'])->assertForbidden();
        $this->actingAs($member)->delete(route('team.destroy'))->assertForbidden();
    }

    public function test_the_captain_can_transfer_the_role_rename_and_renew_the_code(): void
    {
        [$captain, $member, $team] = $this->teamWithCaptain();
        $oldCode = $team->invite_code;

        $this->actingAs($captain)->put(route('team.update'), ['name' => 'Renamed Team'])->assertSessionHas('success');
        $this->actingAs($captain)->post(route('team.code'))->assertSessionHas('success');
        $this->actingAs($captain)->post(route('team.captain', $member))->assertSessionHas('success');

        $team->refresh();
        $this->assertSame('Renamed Team', $team->name);
        $this->assertNotSame($oldCode, $team->invite_code);
        $this->assertSame($member->id, $team->captain_id);
    }

    public function test_the_next_member_leads_when_the_captain_leaves(): void
    {
        [$captain, $member, $team] = $this->teamWithCaptain();

        $this->actingAs($captain)->delete(route('team.leave'))->assertRedirect(route('team.show'));

        $this->assertNull($captain->fresh()->team_id);
        $this->assertSame($member->id, $team->fresh()->captain_id);
    }

    public function test_an_empty_team_without_history_is_deleted_when_the_last_member_leaves(): void
    {
        $captain = User::factory()->create();
        $team = Team::factory()->create(['captain_id' => $captain->id]);
        $captain->update(['team_id' => $team->id]);

        $this->actingAs($captain)->delete(route('team.leave'));

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_a_team_with_submissions_cannot_be_disbanded(): void
    {
        [$captain, , $team] = $this->teamWithCaptain();
        $competition = $this->makeCompetition();
        $challenge = $this->makeChallenge($competition);
        Submission::factory()->create(['team_id' => $team->id, 'challenge_id' => $challenge->id, 'user_id' => $captain->id]);

        $this->actingAs($captain)->delete(route('team.destroy'))->assertSessionHas('error');

        $this->assertDatabaseHas('teams', ['id' => $team->id]);
    }

    public function test_a_team_without_submissions_can_be_disbanded(): void
    {
        [$captain, $member, $team] = $this->teamWithCaptain();

        $this->actingAs($captain)->delete(route('team.destroy'))->assertRedirect(route('team.show'));

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
        $this->assertNull($member->fresh()->team_id);
    }

    public function test_the_team_pages_render(): void
    {
        [$captain, , $team] = $this->teamWithCaptain();

        $this->get(route('teams.index'))->assertOk()->assertSee($team->name);
        $this->get(route('teams.show', $team))->assertOk()->assertSee($team->name);
        $this->actingAs($captain)->get(route('team.show'))->assertOk()->assertSee($team->invite_code);
    }
}
