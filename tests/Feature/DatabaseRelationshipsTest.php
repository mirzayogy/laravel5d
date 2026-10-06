<?php

namespace Tests\Feature;

use App\Models\Bird;
use App\Models\Contest;
use App\Models\ContestEntry;
use App\Models\HealthRecord;
use App\Models\Judge;
use App\Models\Organizer;
use App\Models\Profile;
use App\Models\Species;
use App\Models\TrainingLog;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function entryAttributes(int $entryNumber = 1): array
    {
        return [
            'entry_number' => $entryNumber,
            'division' => 'novice',
            'total_score' => null,
            'final_rank' => null,
            'registered_at' => now(),
        ];
    }

    public function test_user_has_one_profile(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->for($user)->create();

        $this->assertTrue($user->profile->is($profile));
        $this->assertTrue($profile->user->is($user));
    }

    public function test_user_has_many_birds_and_each_bird_has_an_owner_and_species(): void
    {
        $user = User::factory()->create();
        $species = Species::factory()->create();
        $birds = Bird::factory()->count(3)->for($user, 'owner')->for($species)->create();

        $this->assertCount(3, $user->birds);
        $this->assertCount(3, $species->birds);
        $this->assertTrue($birds->first()->owner->is($user));
        $this->assertTrue($birds->first()->species->is($species));
    }

    public function test_bird_can_reference_a_sire_and_a_dam(): void
    {
        $owner = User::factory()->create();
        $species = Species::factory()->create();

        $sire = Bird::factory()->male()->for($owner, 'owner')->for($species)->create();
        $dam = Bird::factory()->female()->for($owner, 'owner')->for($species)->create();
        $chick = Bird::factory()->for($owner, 'owner')->for($species)->create([
            'sire_id' => $sire->id,
            'dam_id' => $dam->id,
        ]);

        $this->assertTrue($chick->sire->is($sire));
        $this->assertTrue($chick->dam->is($dam));
        $this->assertTrue($sire->offspringAsSire->contains($chick));
        $this->assertTrue($dam->offspringAsDam->contains($chick));
    }

    public function test_user_and_species_reach_records_through_birds(): void
    {
        $user = User::factory()->create();
        $species = Species::factory()->create();
        $bird = Bird::factory()->for($user, 'owner')->for($species)->create();
        $otherBird = Bird::factory()->create();

        HealthRecord::factory()->count(2)->for($bird)->create();
        HealthRecord::factory()->for($otherBird)->create();
        TrainingLog::factory()->count(3)->for($bird)->create();
        TrainingLog::factory()->for($otherBird)->create();

        $this->assertCount(2, $user->healthRecords);
        $this->assertCount(3, $user->trainingLogs);
        $this->assertCount(2, $species->healthRecords);
    }

    public function test_judges_and_species_form_a_many_to_many_relationship(): void
    {
        $judge = Judge::factory()->create();
        $species = Species::factory()->count(2)->create();

        $judge->species()->attach($species->pluck('id')->all());

        $this->assertCount(2, $judge->species);
        $this->assertTrue($species->first()->judges->contains($judge));
    }

    public function test_birds_enter_contests_with_pivot_data(): void
    {
        $bird = Bird::factory()->create();
        $contest = Contest::factory()->finished()->create();

        $bird->contests()->attach($contest->id, [
            'entry_number' => 7,
            'division' => 'champion',
            'total_score' => 92.5,
            'final_rank' => 2,
            'registered_at' => now(),
        ]);

        $entry = $bird->contests()->first()->entry;

        $this->assertInstanceOf(ContestEntry::class, $entry);
        $this->assertSame(7, $entry->entry_number);
        $this->assertSame('champion', $entry->division);
        $this->assertSame('92.50', $entry->total_score);
        $this->assertSame(2, $entry->final_rank);
        $this->assertTrue($contest->birds->first()->is($bird));
    }

    public function test_contest_belongs_to_an_organizer_and_a_chief_judge(): void
    {
        $organizer = Organizer::factory()->create();
        $judge = Judge::factory()->create();
        $contest = Contest::factory()->create([
            'organizer_id' => $organizer->id,
            'chief_judge_id' => $judge->id,
        ]);

        $this->assertTrue($contest->organizer->is($organizer));
        $this->assertTrue($contest->chiefJudge->is($judge));
        $this->assertTrue($organizer->contests->contains($contest));
        $this->assertTrue($judge->chairedContests->contains($contest));
    }

    public function test_a_bird_cannot_enter_the_same_contest_twice(): void
    {
        $bird = Bird::factory()->create();
        $contest = Contest::factory()->create();

        $bird->contests()->attach($contest->id, $this->entryAttributes(1));

        $this->expectException(QueryException::class);

        $bird->contests()->attach($contest->id, $this->entryAttributes(2));
    }

    public function test_entry_numbers_are_unique_within_a_contest(): void
    {
        $contest = Contest::factory()->create();
        [$first, $second] = Bird::factory()->count(2)->create();

        $contest->birds()->attach($first->id, $this->entryAttributes(5));

        $this->expectException(QueryException::class);

        $contest->birds()->attach($second->id, $this->entryAttributes(5));
    }

    public function test_deleting_a_user_cascades_to_owned_data(): void
    {
        $user = User::factory()->create();
        Profile::factory()->for($user)->create();
        $bird = Bird::factory()->for($user, 'owner')->create();
        HealthRecord::factory()->for($bird)->create();
        TrainingLog::factory()->for($bird)->create();
        $bird->contests()->attach(Contest::factory()->create()->id, $this->entryAttributes());

        $user->delete();

        $this->assertDatabaseCount('profiles', 0);
        $this->assertDatabaseCount('birds', 0);
        $this->assertDatabaseCount('health_records', 0);
        $this->assertDatabaseCount('training_logs', 0);
        $this->assertDatabaseCount('bird_contest', 0);
    }

    public function test_deleting_a_sire_keeps_the_offspring(): void
    {
        $sire = Bird::factory()->male()->create();
        $chick = Bird::factory()->create(['sire_id' => $sire->id]);

        $sire->delete();

        $this->assertNull($chick->fresh()->sire_id);
    }

    public function test_a_species_that_still_has_birds_cannot_be_deleted(): void
    {
        $species = Species::factory()->create();
        Bird::factory()->for($species)->create();

        $this->expectException(QueryException::class);

        $species->delete();
    }

    public function test_an_organizer_that_still_has_contests_cannot_be_deleted(): void
    {
        $organizer = Organizer::factory()->create();
        Contest::factory()->create(['organizer_id' => $organizer->id]);

        $this->expectException(QueryException::class);

        $organizer->delete();
    }

    public function test_deleting_a_chief_judge_clears_the_link_on_the_contest(): void
    {
        $judge = Judge::factory()->create();
        $contest = Contest::factory()->create(['chief_judge_id' => $judge->id]);

        $judge->delete();

        $this->assertNull($contest->fresh()->chief_judge_id);
    }

    public function test_database_seeder_builds_a_consistent_dataset(): void
    {
        $this->seed();

        $demo = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertNotNull($demo->profile);
        $this->assertCount(6, $demo->birds);
        $this->assertSame(18, $demo->healthRecords()->count());
        $this->assertSame(24, $demo->trainingLogs()->count());
        $this->assertSame(8, Species::count());
        $this->assertSame(6, Contest::count());

        foreach (Contest::with('birds')->get() as $contest) {
            $numbers = $contest->birds->map(fn (Bird $bird) => $bird->entry->entry_number);

            $this->assertSame($numbers->count(), $numbers->unique()->count());

            if ($contest->status === 'finished') {
                $ranks = $contest->birds->map(fn (Bird $bird) => $bird->entry->final_rank)->sort()->values()->all();

                $this->assertSame(range(1, count($ranks)), $ranks);
            }
        }
    }
}
