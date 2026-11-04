<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Habit;
use App\Models\MoodEntry;
use App\Models\User;
use App\Services\CalendarService;
use App\Services\StreakService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Weekly goals ("3 days a week") and rest days. The test clock is Wednesday 28 Oct 2026,
 * so this week runs Monday 26 to Sunday 1 Nov.
 */
class FlexibleScheduleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private StreakService $streaks;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        CarbonImmutable::setTestNow('2026-10-28 12:00:00');
        $this->user = $this->signIn();
        $this->streaks = app(StreakService::class);
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function habit(array $attrs = []): Habit
    {
        return Habit::factory()->create($attrs + ['user_id' => $this->user->id, 'target_count' => 1, 'unit' => 'times', 'is_active' => true]);
    }

    private function logDays(Habit $habit, array $daysAgo): void
    {
        foreach ($daysAgo as $d) {
            $habit->logs()->create(['logged_date' => CarbonImmutable::now()->subDays($d)->toDateString(), 'value' => $habit->target_count]);
        }
    }

    private function rest(int $daysAgo): void
    {
        $this->user->restDays()->create(['rest_date' => CarbonImmutable::now()->subDays($daysAgo)->toDateString()]);
    }

    private function overall(): array
    {
        return $this->streaks->overall($this->user, $this->user->today());
    }

    // ---- rest days and the streak ----

    public function test_a_rest_day_bridges_the_streak_without_counting(): void
    {
        $habit = $this->habit();
        $this->logDays($habit, [0, 2, 3]); // yesterday missed

        $this->assertSame(1, $this->overall()['current']);

        $this->rest(1);

        $this->assertSame(3, $this->overall()['current']); // the rest day itself adds nothing
        $this->assertSame(3, $this->overall()['best']);
        $this->assertSame(3, $this->streaks->habitStreak($habit, $this->user->today()));
    }

    public function test_the_best_streak_also_bridges_a_rest_day(): void
    {
        $habit = $this->habit();
        $this->logDays($habit, [6, 5, 3, 2]); // day 4 missed
        $this->assertSame(2, $this->overall()['best']);

        $this->rest(4);

        $this->assertSame(4, $this->overall()['best']);
    }

    public function test_resting_today_keeps_yesterdays_streak(): void
    {
        $this->logDays($this->habit(), [1, 2]);
        $this->rest(0);

        $this->assertSame(2, $this->overall()['current']);
    }

    public function test_two_missed_days_still_break_the_streak_when_only_one_is_rest(): void
    {
        $this->logDays($this->habit(), [0, 3, 4]); // days 1 and 2 missed
        $this->rest(1);

        $this->assertSame(1, $this->overall()['current']);
    }

    // ---- weekly goals ----

    public function test_a_weekly_habit_streak_counts_weeks_that_reach_the_goal(): void
    {
        $habit = $this->habit(['weekly_goal' => 3]);
        $this->logDays($habit, [0, 1, 2]);   // Mon 26 - Wed 28: this week met
        $this->logDays($habit, [7, 8, 9]);   // Wed 21 - Mon 19: last week met

        $this->assertSame(2, $this->streaks->habitStreak($habit, $this->user->today()));
    }

    public function test_an_unfinished_week_does_not_break_a_weekly_streak(): void
    {
        $habit = $this->habit(['weekly_goal' => 3]);
        $this->logDays($habit, [0, 1]);       // two so far this week
        $this->logDays($habit, [7, 8, 9]);    // last week met

        $this->assertSame(1, $this->streaks->habitStreak($habit, $this->user->today()));
    }

    public function test_a_missed_week_ends_a_weekly_streak(): void
    {
        $habit = $this->habit(['weekly_goal' => 3]);
        $this->logDays($habit, [0, 1, 2]);    // this week met
        $this->logDays($habit, [7, 8]);       // last week only two
        $this->logDays($habit, [14, 15, 16]); // the week before met, but cut off

        $this->assertSame(1, $this->streaks->habitStreak($habit, $this->user->today()));
    }

    public function test_week_counts_only_cover_weekly_habits_and_this_week(): void
    {
        $weekly = $this->habit(['weekly_goal' => 3]);
        $daily = $this->habit();
        $this->logDays($weekly, [0, 1, 9]);
        $this->logDays($daily, [0]);

        $counts = $this->streaks->weekCounts(collect([$weekly, $daily]), $this->user->today());

        $this->assertSame([$weekly->id => 2], $counts);
    }

    public function test_the_calendar_does_not_expect_a_weekly_habit_every_day(): void
    {
        $daily = $this->habit(['created_at' => '2026-10-01']);
        $weekly = $this->habit(['weekly_goal' => 2, 'created_at' => '2026-10-01']);
        $this->logDays($weekly, [1]);

        $days = collect(app(CalendarService::class)->month($this->user, CarbonImmutable::parse('2026-10-01'), $this->user->today()))
            ->flatten(1)->keyBy('date');

        $this->assertSame(1, $days['2026-10-20']['total']);  // only the daily habit
        $this->assertSame(2, $days['2026-10-27']['total']);  // the weekly habit was logged that day
        $this->assertSame(1, $days['2026-10-27']['done']);
    }

    // ---- taking a rest day ----

    public function test_a_rest_day_can_be_taken_for_today(): void
    {
        $this->post('/rest-days')->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('rest_days', ['user_id' => $this->user->id, 'rest_date' => '2026-10-28']);
    }

    public function test_only_one_rest_day_per_week(): void
    {
        $this->post('/rest-days');
        $this->post('/rest-days', ['date' => '2026-10-27'])->assertSessionHas('error');

        $this->assertSame(1, $this->user->restDays()->count());
    }

    public function test_a_rest_day_a_week_ago_is_allowed_again(): void
    {
        $this->rest(7);
        $this->post('/rest-days')->assertSessionHas('success');

        $this->assertSame(2, $this->user->restDays()->count());
    }

    public function test_a_rest_day_cannot_be_older_than_yesterday_or_in_the_future(): void
    {
        $this->post('/rest-days', ['date' => '2026-10-26'])->assertSessionHasErrors('date');
        $this->post('/rest-days', ['date' => '2026-10-29'])->assertSessionHasErrors('date');
        $this->post('/rest-days', ['date' => 'garbage'])->assertSessionHasErrors('date');

        $this->assertSame(0, $this->user->restDays()->count());
    }

    public function test_a_day_with_a_completed_habit_does_not_need_a_rest_day(): void
    {
        $this->logDays($this->habit(), [0]);

        $this->post('/rest-days')->assertSessionHas('error');
        $this->assertSame(0, $this->user->restDays()->count());
    }

    public function test_a_rest_day_can_be_taken_back(): void
    {
        $this->rest(0);

        $this->delete('/rest-days/2026-10-28')->assertSessionHas('success');
        $this->assertSame(0, $this->user->restDays()->count());
    }

    public function test_nobody_can_remove_someone_elses_rest_day(): void
    {
        $other = User::factory()->create();
        $other->restDays()->create(['rest_date' => '2026-10-28']);

        $this->delete('/rest-days/2026-10-28');

        $this->assertSame(1, $other->restDays()->count());
    }

    public function test_rest_days_go_away_with_the_user(): void
    {
        $this->rest(0);
        $this->user->delete();

        $this->assertDatabaseCount('rest_days', 0);
    }

    // ---- the habit form ----

    public function test_a_habit_can_be_saved_with_a_weekly_goal(): void
    {
        $category = Category::factory()->create();
        $payload = ['category_id' => $category->id, 'name' => 'Run', 'target_count' => 1, 'unit' => 'sessions', 'is_active' => 1];

        $this->post('/habits', $payload + ['weekly_goal' => 3]);
        $this->post('/habits', ['name' => 'Read'] + $payload + ['weekly_goal' => 0]);

        $this->assertSame(3, Habit::where('name', 'Run')->value('weekly_goal'));
        $this->assertNull(Habit::where('name', 'Read')->value('weekly_goal'));
    }

    public function test_a_weekly_goal_must_be_between_one_and_six(): void
    {
        $category = Category::factory()->create();

        $this->post('/habits', ['category_id' => $category->id, 'name' => 'Run', 'target_count' => 1, 'unit' => 'times', 'weekly_goal' => 7])
            ->assertSessionHasErrors('weekly_goal');
    }

    public function test_the_edit_form_offers_the_frequency(): void
    {
        $habit = $this->habit(['weekly_goal' => 4]);

        $this->get("/habits/{$habit->id}/edit")->assertOk()->assertSee('How often')->assertSee('4 days a week');
    }

    // ---- what the dashboard shows ----

    public function test_the_dashboard_shows_weekly_progress_and_the_rest_day_button(): void
    {
        $habit = $this->habit(['weekly_goal' => 3, 'name' => 'Swim']);
        $this->logDays($habit, [1, 0]);

        $this->get('/')->assertOk()->assertSee('2 of 3 days this week')->assertSee('Take a rest day');
    }

    public function test_the_dashboard_says_when_the_rest_day_is_used(): void
    {
        $this->rest(1);

        $this->get('/')->assertSee('Rest day used on Tuesday')->assertDontSee('Take a rest day');
    }

    public function test_a_rest_yesterday_removes_the_log_yesterday_nudge(): void
    {
        $this->habit(['created_at' => '2026-10-20']);

        $this->get('/')->assertSee('Log yesterday')->assertSee('It was a rest day');

        $this->rest(1);

        $this->get('/')->assertDontSee('Log yesterday');
    }

    public function test_the_check_in_response_carries_the_weekly_count(): void
    {
        $habit = $this->habit(['weekly_goal' => 3]);
        $this->logDays($habit, [1]);

        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'toggle'])
            ->assertOk()->assertJson(['completed' => true, 'week_done' => 2]);

        $daily = $this->habit();
        $this->postJson("/habits/{$daily->id}/log", ['mode' => 'toggle'])->assertJson(['week_done' => null]);
    }

    // ---- mood history ----

    public function test_the_mood_history_shows_five_rows_and_can_be_extended(): void
    {
        foreach (range(1, 8) as $i) {
            MoodEntry::factory()->create(['user_id' => $this->user->id, 'entry_date' => CarbonImmutable::now()->subDays($i)->toDateString(), 'mood_level' => 3]);
        }

        $html = $this->get('/mood')->assertOk()->getContent();

        $this->assertSame(8, substr_count($html, 'data-history-row'));
        $this->assertSame(3, preg_match_all('/data-history-row class="hidden"/', $html));
        $this->assertStringContainsString('Showing 5 of 8', $html);
        $this->assertStringContainsString('Show 3 more', $html);
        $this->assertStringContainsString('20 rows', $html);
    }

    public function test_the_mood_history_has_no_extend_button_when_everything_fits(): void
    {
        MoodEntry::factory()->create(['user_id' => $this->user->id, 'entry_date' => '2026-10-27', 'mood_level' => 4]);

        $this->get('/mood')->assertOk()->assertSee('Showing 1 of 1');
    }
}
