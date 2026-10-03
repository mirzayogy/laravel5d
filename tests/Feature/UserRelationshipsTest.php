<?php

namespace Tests\Feature;

use App\Models\Cycle;
use App\Models\JournalEntry;
use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\MoodEntry;
use App\Models\PeriodLog;
use App\Models\Profile;
use App\Models\Reminder;
use App\Models\Symptom;
use App\Models\SymptomLog;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_profile_cycles_and_period_logs_through_cycles(): void
    {
        $user = User::factory()->create();
        Profile::factory()->for($user)->create();
        $cycle = Cycle::factory()->for($user)->create();
        PeriodLog::factory()->for($user)->for($cycle)->count(3)->sequence(
            ['log_date' => '2026-10-01'], ['log_date' => '2026-10-02'], ['log_date' => '2026-10-03'],
        )->create();

        $this->assertInstanceOf(Profile::class, $user->profile);
        $this->assertCount(1, $user->cycles);
        $this->assertCount(3, $cycle->periodLogs);
        $this->assertCount(3, $user->periodLogsThroughCycles);
    }

    public function test_user_symptoms_expose_pivot_data(): void
    {
        $user = User::factory()->create();
        $symptom = Symptom::factory()->create();
        SymptomLog::factory()->for($user)->for($symptom)->create(['log_date' => '2026-10-02', 'severity' => 3]);

        $this->assertSame(3, $user->symptoms()->first()->pivot->severity);
        $this->assertCount(1, $symptom->symptomLogs);
    }

    public function test_symptom_log_is_unique_per_user_symptom_and_day(): void
    {
        $user = User::factory()->create();
        $symptom = Symptom::factory()->create();
        SymptomLog::factory()->for($user)->for($symptom)->create(['log_date' => '2026-10-02']);

        $this->expectException(QueryException::class);
        SymptomLog::factory()->for($user)->for($symptom)->create(['log_date' => '2026-10-02']);
    }

    public function test_mood_entry_may_have_one_journal_entry(): void
    {
        $user = User::factory()->create();
        $mood = MoodEntry::factory()->for($user)->create();
        $journal = JournalEntry::factory()->for($user)->create(['mood_entry_id' => $mood->id]);

        $this->assertTrue($mood->journalEntry->is($journal));
        $this->assertTrue($journal->moodEntry->is($mood));
    }

    public function test_deleting_a_user_cascades_and_nulls_reminder_medication(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->for($user)->create();
        MedicationLog::factory()->for($medication)->create();
        $reminder = Reminder::factory()->for($user)->create(['medication_id' => $medication->id]);

        $medication->delete();
        $this->assertNull($reminder->fresh()->medication_id);
        $this->assertDatabaseCount('medication_logs', 0);

        $user->delete();
        $this->assertDatabaseCount('reminders', 0);
    }
}
