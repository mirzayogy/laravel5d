<?php

namespace Tests\Feature;

use App\Models\Habit;
use App\Models\User;
use App\Notifications\HabitReminder;
use Carbon\CarbonImmutable;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DeliveryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Notification::fake();
        // 07:30 in Makassar (UTC+8), a Wednesday
        CarbonImmutable::setTestNow('2026-10-27 23:30:00');
        $this->user = $this->signIn();
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function reminder(string $time, array $days = [3], array $habit = [], ?User $owner = null)
    {
        $habit = Habit::factory()->create($habit + ['user_id' => ($owner ?? $this->user)->id, 'target_count' => 1, 'is_active' => true]);

        return $habit->reminders()->create(['remind_at' => $time, 'days_of_week' => $days]);
    }

    public function test_a_due_reminder_is_emailed_once_a_day(): void
    {
        $reminder = $this->reminder('07:30', habit: ['name' => 'Morning run']);

        $this->artisan('habitude:send-reminders')->expectsOutputToContain('Sent 1 reminder email')->assertSuccessful();

        Notification::assertSentTo($this->user, HabitReminder::class, fn ($n) => $n->habits->pluck('name')->all() === ['Morning run']);
        $this->assertSame('2026-10-28', $reminder->fresh()->last_sent_on->toDateString());

        // running again in the same minute (or the next) must not send it twice
        $this->artisan('habitude:send-reminders');
        Notification::assertCount(1);
    }

    public function test_it_catches_up_a_few_minutes_late_but_not_early_or_very_late(): void
    {
        $this->reminder('07:27', habit: ['name' => 'A little late']);   // 3 minutes ago: still sent
        $this->reminder('07:20', habit: ['name' => 'Too late']);        // 10 minutes ago: skipped
        $this->reminder('07:45', habit: ['name' => 'Not yet']);         // in the future: skipped

        $this->artisan('habitude:send-reminders');

        Notification::assertSentTo($this->user, HabitReminder::class, fn ($n) => $n->habits->pluck('name')->all() === ['A little late']);
        Notification::assertCount(1);
    }

    public function test_habits_due_together_arrive_as_one_email(): void
    {
        $this->reminder('07:30', habit: ['name' => 'Stretch']);
        $this->reminder('07:30', habit: ['name' => 'Water']);

        $this->artisan('habitude:send-reminders');

        Notification::assertCount(1);
        Notification::assertSentTo($this->user, HabitReminder::class, fn ($n) => $n->habits->count() === 2);
    }

    public function test_it_skips_what_should_not_be_sent(): void
    {
        $this->reminder('07:30', [1], ['name' => 'Monday only']);                       // wrong weekday
        $off = $this->reminder('07:30', habit: ['name' => 'Switched off']);
        $off->update(['is_enabled' => false]);
        $this->reminder('07:30', habit: ['name' => 'Paused', 'is_active' => false]);
        $done = $this->reminder('07:30', habit: ['name' => 'Already done']);
        $done->habit->logs()->create(['logged_date' => '2026-10-28', 'value' => 1]);
        $deleted = $this->reminder('07:30', habit: ['name' => 'Deleted']);
        $deleted->habit->delete();

        $this->artisan('habitude:send-reminders')->expectsOutputToContain('No reminders due');

        Notification::assertNothingSent();
    }

    public function test_it_respects_the_email_switch_and_each_persons_timezone(): void
    {
        $this->user->profile->update(['email_reminders' => false]);
        $this->reminder('07:30', habit: ['name' => 'Muted']);

        $jakarta = User::factory()->create();
        $jakarta->profile()->create(['timezone' => 'Asia/Jakarta', 'onboarded_at' => now()]); // 06:30 for them
        $this->reminder('07:30', habit: ['name' => 'Not yet in Jakarta'], owner: $jakarta);
        $this->reminder('06:30', habit: ['name' => 'Right now in Jakarta'], owner: $jakarta);

        $this->artisan('habitude:send-reminders');

        Notification::assertNotSentTo($this->user, HabitReminder::class);
        Notification::assertSentTo($jakarta, HabitReminder::class, fn ($n) => $n->habits->pluck('name')->all() === ['Right now in Jakarta']);
    }

    public function test_the_email_reads_well(): void
    {
        $habit = Habit::factory()->create(['user_id' => $this->user->id, 'name' => 'Drink water', 'target_count' => 8, 'unit' => 'glasses']);
        $mail = (new HabitReminder(collect([$habit])))->toMail($this->user);

        $this->assertSame('Time for Drink water', $mail->subject);
        $this->assertStringContainsString('Drink water (8 glasses)', implode("\n", $mail->introLines));
        $this->assertSame(route('dashboard'), $mail->actionUrl);

        $two = (new HabitReminder(collect([$habit, $habit])))->toMail($this->user);
        $this->assertSame('2 habits are waiting for you', $two->subject);
    }

    public function test_the_scheduler_runs_the_command_every_minute(): void
    {
        $events = collect(app(Schedule::class)->events());

        $this->assertTrue($events->contains(fn ($e) => str_contains($e->command, 'habitude:send-reminders') && $e->expression === '* * * * *'));
        $this->assertTrue($events->contains(fn ($e) => str_contains($e->command, 'model:prune')));
    }

    public function test_the_profile_stores_the_email_switch(): void
    {
        $base = ['name' => 'A', 'email' => $this->user->email, 'timezone' => 'UTC', 'daily_goal' => 3];

        $this->put('/profile', $base);
        $this->assertFalse($this->user->profile->fresh()->email_reminders);

        $this->put('/profile', $base + ['email_reminders' => '1']);
        $this->assertTrue($this->user->profile->fresh()->email_reminders);
    }

    // ---- browser notifications feed ----

    public function test_the_notifier_feed_lists_only_what_is_still_to_do_today(): void
    {
        $this->reminder('07:30', habit: ['name' => 'Open habit']);
        $this->reminder('09:00', habit: ['name' => 'Later']);
        $done = $this->reminder('07:00', habit: ['name' => 'Finished']);
        $done->habit->logs()->create(['logged_date' => '2026-10-28', 'value' => 1]);
        $this->reminder('07:30', [1], ['name' => 'Another weekday']);

        $json = $this->getJson('/reminders/today')->assertOk()->json();

        $this->assertSame('07:30', $json['now']);
        $this->assertSame('2026-10-28', $json['date']);
        $this->assertSame(['Open habit', 'Later'], array_column($json['items'], 'habit'));
        $this->assertSame('07:30', $json['items'][0]['time']);
    }

    public function test_the_notifier_feed_is_private(): void
    {
        auth()->logout();

        $this->getJson('/reminders/today')->assertUnauthorized();
    }

    public function test_the_profile_page_has_the_delivery_controls(): void
    {
        $this->get('/profile')->assertOk()->assertSee('Email me my reminders')->assertSee('data-notify-setting', false)->assertSee('data-install', false);
    }
}
