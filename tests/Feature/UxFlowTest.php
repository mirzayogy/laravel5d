<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Habit;
use App\Models\User;
use App\Services\AchievementService;
use App\Services\StreakService;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class UxFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        CarbonImmutable::setTestNow('2026-10-28 03:00:00'); // 11:00 in Makassar, a Wednesday
        $this->user = $this->signIn();
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

    // ---- navigation ----

    public function test_the_track_and_master_sections_start_open(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertMatchesRegularExpression('/<div class="bm-section" data-bm-section\s+data-open\s*>\s*<button[^>]*>Track/', $html);
    }

    // ---- undo ----

    public function test_a_deleted_habit_can_be_restored_by_its_owner_only(): void
    {
        $habit = $this->habit();
        $this->delete("/habits/{$habit->id}");

        $this->signIn(User::factory()->create());
        $this->postJson("/habits/{$habit->id}/restore")->assertNotFound();
        $this->assertSoftDeleted($habit);

        $this->actingAs($this->user)->postJson("/habits/{$habit->id}/restore")->assertOk();
        $this->assertNotSoftDeleted($habit);
    }

    public function test_the_delete_redirect_carries_the_undo_link_to_the_toast(): void
    {
        $habit = $this->habit(['name' => 'Walk']);

        $this->followingRedirects()->delete("/habits/{$habit->id}")
            ->assertSee('data-flash="success"', false)
            ->assertSee('data-undo="'.route('habits.restore', $habit->id).'"', false);
    }

    public function test_a_deleted_habit_disappears_from_every_number_but_comes_back_with_undo(): void
    {
        $habit = $this->habit();
        foreach ([0, 1, 2] as $d) {
            $habit->logs()->create(['logged_date' => CarbonImmutable::now('Asia/Makassar')->subDays($d)->toDateString(), 'value' => 1]);
        }
        $streak = fn () => app(StreakService::class)->overall($this->user, $this->user->today())['current'];
        $this->assertSame(3, $streak());

        $habit->delete();
        $this->assertSame(0, $streak());
        $this->assertCount(0, $this->get('/')->viewData('activity'));
        $this->assertSame(0, app(AchievementService::class)->metrics($this->user)['logs']);
        $this->assertSame(0, $habit->category->habitLogs()->count()); // has-many-through skips trashed habits

        $habit->restore();
        $this->assertSame(3, $streak());
    }

    public function test_a_category_with_only_deleted_habits_still_cannot_be_removed(): void
    {
        $admin = $this->signIn(User::factory()->admin()->create());
        $habit = Habit::factory()->create(['user_id' => $admin->id]);
        $habit->delete();

        $this->delete("/admin/categories/{$habit->category_id}")->assertSessionHas('error');
        $this->assertModelExists($habit->category);
    }

    public function test_habits_deleted_more_than_30_days_ago_are_pruned(): void
    {
        $old = $this->habit();
        $recent = $this->habit();
        $old->delete();
        $old->forceFill(['deleted_at' => now()->subDays(31)])->saveQuietly();
        $recent->delete();

        $this->artisan('model:prune', ['--model' => [Habit::class]])->assertSuccessful();

        $this->assertDatabaseMissing('habits', ['id' => $old->id]);
        $this->assertSoftDeleted($recent);
    }

    // ---- today's list order ----

    public function test_todays_habits_are_ordered_by_reminder_time_in_day_parts(): void
    {
        $evening = $this->habit(['name' => 'Evening stretch']);
        $evening->reminders()->create(['remind_at' => '19:30', 'days_of_week' => [3]]);
        $morning = $this->habit(['name' => 'Morning run']);
        $morning->reminders()->create(['remind_at' => '06:15', 'days_of_week' => [3]]);
        $anytime = $this->habit(['name' => 'Anytime read']);
        $otherDay = $this->habit(['name' => 'Monday only']);
        $otherDay->reminders()->create(['remind_at' => '07:00', 'days_of_week' => [1]]); // not today (Wednesday)

        $rows = $this->get('/')->assertOk()->viewData('rows');

        $this->assertSame(['Morning run', 'Evening stretch', 'Anytime read', 'Monday only'], $rows->pluck('habit.name')->all());
        $this->assertSame(['morning', 'evening', 'anytime', 'anytime'], $rows->pluck('group')->all());
        $this->assertSame('06:15', $rows[0]['time']);
        $this->get('/')->assertSee('data-today-group="morning"', false)->assertSee('Anytime');
    }

    // ---- quick add ----

    public function test_quick_add_needs_only_a_name_and_a_category(): void
    {
        $category = Category::factory()->create(['slug' => 'stay-hydrated']);

        $this->post('/habits', ['quick' => 1, 'name' => 'Drink water', 'category_id' => $category->id, 'back' => url('/')])
            ->assertRedirect(url('/'))
            ->assertSessionHas('success');

        $habit = Habit::firstWhere('name', 'Drink water');
        $this->assertSame([8, 'glasses', true], [$habit->target_count, $habit->unit, $habit->is_active]);
    }

    public function test_quick_add_falls_back_for_unknown_categories_and_never_redirects_off_site(): void
    {
        $category = Category::factory()->create(['slug' => 'custom']);

        $this->post('/habits', ['quick' => 1, 'name' => 'Something', 'category_id' => $category->id, 'back' => 'https://evil.example/phish'])
            ->assertRedirect(route('habits.index'));

        $habit = Habit::firstWhere('name', 'Something');
        $this->assertSame([1, 'times'], [$habit->target_count, $habit->unit]);
        $this->post('/habits', ['quick' => 1, 'category_id' => $category->id])->assertSessionHasErrors('name');
    }

    public function test_the_dashboard_and_habits_pages_offer_quick_add(): void
    {
        Category::factory()->create(['name' => 'Mindfulness', 'slug' => 'mindfulness']);

        foreach (['/', '/habits'] as $url) {
            $this->get($url)->assertOk()->assertSee('id="quick-add"', false)->assertSee('More options')->assertSee('Meditate');
        }
    }

    // ---- no dead ends on the sign-in pages ----

    public function test_terms_and_privacy_pages_exist_and_are_linked(): void
    {
        auth()->logout();

        $this->get('/terms')->assertOk()->assertSee('Terms of Service')->assertSee('beta preview');
        $this->get('/privacy')->assertOk()->assertSee('Privacy Policy');
        $this->get('/login')->assertSee(route('terms'), false)->assertSee(route('privacy'), false)->assertDontSee('data-soon', false);
        $this->get('/register')->assertDontSee('Single sign-on')->assertSee(route('privacy'), false);
    }

    // ---- password reset ----

    public function test_a_reset_link_is_sent_and_the_answer_does_not_reveal_accounts(): void
    {
        auth()->logout();
        Notification::fake();
        $user = User::factory()->create(['email' => 'real@example.com']);

        $known = $this->post('/forgot-password', ['email' => 'real@example.com']);
        $unknown = $this->post('/forgot-password', ['email' => 'nobody@example.com']);

        Notification::assertSentTo($user, ResetPassword::class);
        Notification::assertCount(1);
        $this->assertSame(session('status'), 'If that email has an account, a reset link is on its way. It works for 60 minutes.');
        $known->assertSessionHas('status');
        $unknown->assertSessionHas('status');
    }

    public function test_reset_link_requests_are_throttled(): void
    {
        auth()->logout();

        foreach (range(1, 3) as $i) {
            $this->post('/forgot-password', ['email' => 'a@example.com'])->assertSessionDoesntHaveErrors();
        }
        $this->post('/forgot-password', ['email' => 'a@example.com'])->assertSessionHasErrors('email');
    }

    public function test_the_password_can_be_changed_with_a_valid_token_only(): void
    {
        auth()->logout();
        $user = User::factory()->create(['email' => 'real@example.com']);
        $token = Password::createToken($user);

        $this->get("/reset-password/{$token}?email=real@example.com")->assertOk()->assertSee('Choose a new password');

        $this->post('/reset-password', ['token' => 'wrong', 'email' => 'real@example.com', 'password' => 'brand-new-pass', 'password_confirmation' => 'brand-new-pass'])
            ->assertSessionHasErrors('email');
        $this->post('/reset-password', ['token' => $token, 'email' => 'real@example.com', 'password' => 'short', 'password_confirmation' => 'short'])
            ->assertSessionHasErrors('password');

        $this->post('/reset-password', ['token' => $token, 'email' => 'real@example.com', 'password' => 'brand-new-pass', 'password_confirmation' => 'brand-new-pass'])
            ->assertRedirect('/login')->assertSessionHas('status');
        $this->assertTrue(Hash::check('brand-new-pass', $user->fresh()->password));

        // a token works once
        $this->post('/reset-password', ['token' => $token, 'email' => 'real@example.com', 'password' => 'another-new-pass', 'password_confirmation' => 'another-new-pass'])
            ->assertSessionHasErrors('email');
    }

    public function test_login_links_to_the_reset_page(): void
    {
        auth()->logout();

        $this->get('/login')->assertSee(route('password.request'), false);
        $this->get('/forgot-password')->assertOk()->assertSee('Forgot your password?');
    }

    // ---- daily check-in: mood leads to a journal prompt ----

    public function test_saving_a_mood_offers_a_journal_prompt_that_fits_the_day(): void
    {
        $json = $this->postJson('/mood', ['mood_level' => 2])->assertOk()->json();

        $this->assertSame(route('journal.create', ['mood' => 2, 'date' => '2026-10-28']), $json['journal_url']);
        $this->assertFalse($json['has_journal']);

        $low = $this->get($json['journal_url'])->assertOk()->viewData('prompt');
        $this->assertContains($low, config('habitude.prompts.low'));
        $this->assertContains($this->get('/journal/create?mood=5')->viewData('prompt'), config('habitude.prompts.high'));
        $this->assertContains($this->get('/journal/create?mood=3')->viewData('prompt'), config('habitude.prompts.mid'));
    }

    public function test_the_journal_form_ignores_bad_prompt_and_date_input(): void
    {
        $this->assertNull($this->get('/journal/create')->viewData('prompt'));
        $this->assertNull($this->get('/journal/create?mood=9')->viewData('prompt'));
        $this->assertSame('2026-10-28', $this->get('/journal/create?date=garbage')->assertOk()->viewData('date')->toDateString());
        $this->assertSame('2026-10-28', $this->get('/journal/create?date=2027-01-01')->viewData('date')->toDateString()); // not the future
        $this->assertSame('2026-10-20', $this->get('/journal/create?date=2026-10-20')->viewData('date')->toDateString());
    }

    public function test_the_mood_page_redirect_carries_a_write_about_it_link_until_a_journal_exists(): void
    {
        $this->post('/mood', ['mood_level' => 4])
            ->assertSessionHas('action_url', route('journal.create', ['mood' => 4, 'date' => '2026-10-28']))
            ->assertSessionHas('action_label', 'Write about it');

        $this->post('/journal', ['entry_date' => '2026-10-28', 'title' => 'Done', 'content' => 'Written']);
        $this->post('/mood', ['mood_level' => 5])->assertSessionMissing('action_url');
        $this->postJson('/mood', ['mood_level' => 5])->assertJson(['has_journal' => true]);
    }

    public function test_the_prompt_shows_on_the_form_and_prefills_the_title(): void
    {
        $this->get('/journal/create?mood=4&date=2026-10-27')
            ->assertOk()
            ->assertSee('A question to start with')
            ->assertSee('Tuesday, 27 October');
    }

    // ---- nudge for a missed day ----

    private function oldHabit(): Habit
    {
        $habit = $this->habit();
        $habit->forceFill(['created_at' => CarbonImmutable::now()->subDays(10)])->save();

        return $habit;
    }

    public function test_it_nudges_when_yesterday_has_no_check_ins(): void
    {
        $this->oldHabit();

        $this->get('/')->assertOk()->assertSee('data-nudge="missed-2026-10-27"', false)->assertSee('Log yesterday');
        $this->assertTrue($this->get('/')->viewData('missedYesterday'));
    }

    public function test_it_does_not_nudge_when_yesterday_is_logged_or_the_habit_is_new_or_you_look_at_a_past_day(): void
    {
        $habit = $this->oldHabit();
        $habit->logs()->create(['logged_date' => '2026-10-27', 'value' => 1]);
        $this->assertFalse($this->get('/')->viewData('missedYesterday'));

        $habit->logs()->delete();
        $this->assertFalse($this->get('/?date=2026-10-20')->viewData('missedYesterday'));

        $habit->forceFill(['created_at' => CarbonImmutable::now()])->save();  // created today: nothing to miss
        $this->assertFalse($this->get('/')->viewData('missedYesterday'));
    }

    public function test_the_celebration_has_what_it_needs(): void
    {
        $this->habit();

        $this->get('/')->assertSee('data-today-date="2026-10-28"', false);
    }
}
