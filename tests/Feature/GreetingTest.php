<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Habit;
use App\Models\User;
use App\Support\Greeting;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Random\Engine\Mt19937;
use Random\Randomizer;
use Tests\TestCase;

class GreetingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        CarbonImmutable::setTestNow('2026-10-28 03:00:00'); // 11:00 in Makassar
        $this->user = $this->signIn(User::factory()->create(['name' => 'Ayu Lestari']));
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function row(string $slug, bool $done = false): array
    {
        $category = Category::firstOrCreate(['slug' => $slug], ['name' => ucfirst($slug), 'color' => '#ff5900']);
        $habit = Habit::factory()->create(['user_id' => $this->user->id, 'category_id' => $category->id]);

        return ['habit' => $habit->load('category'), 'done' => $done];
    }

    private function greet(array $rows, array $streak = ['current' => 0, 'best' => 0], int $seed = 1, string $at = '2026-10-28 11:00:00'): array
    {
        return Greeting::for($this->user, CarbonImmutable::parse($at), collect($rows), $streak, new Randomizer(new Mt19937($seed)));
    }

    public function test_it_greets_by_first_name(): void
    {
        $greeting = $this->greet([$this->row('read-more')]);

        $this->assertStringEndsWith(', Ayu', $greeting['hello']);
        $this->assertStringNotContainsString('Lestari', $greeting['hello']);
    }

    public function test_the_line_follows_the_open_habit(): void
    {
        foreach (range(1, 20) as $seed) {
            $this->assertContains($this->greet([$this->row('read-more')], seed: $seed)['line'], Greeting::CATEGORY_LINES['read-more']);
            $this->assertContains($this->greet([$this->row('health-fitness')], seed: $seed)['line'], Greeting::CATEGORY_LINES['health-fitness']);
        }
    }

    public function test_completed_habits_are_not_asked_about_again(): void
    {
        $rows = [$this->row('health-fitness', done: true), $this->row('read-more')];

        foreach (range(1, 30) as $seed) {
            $line = $this->greet($rows, seed: $seed)['line'];
            $this->assertNotContains($line, Greeting::CATEGORY_LINES['health-fitness']);
            $this->assertTrue(in_array($line, Greeting::CATEGORY_LINES['read-more'], true) || $line === '1 of 2 done. Ready for the next one?', $line);
        }
    }

    public function test_all_done_and_no_habits_have_their_own_lines(): void
    {
        $this->assertContains($this->greet([$this->row('read-more', done: true)])['line'], Greeting::DONE_LINES);
        $this->assertContains($this->greet([])['line'], Greeting::EMPTY_LINES);
    }

    public function test_bedtime_habits_wait_until_the_evening(): void
    {
        $morning = $this->greet([$this->row('better-sleep')], at: '2026-10-28 09:00:00')['line'];
        $night = $this->greet([$this->row('better-sleep')], at: '2026-10-28 21:00:00')['line'];

        $this->assertSame('Ready for today?', $morning);
        $this->assertContains($night, Greeting::CATEGORY_LINES['better-sleep']);
    }

    public function test_the_opener_follows_the_time_of_day(): void
    {
        $openers = fn (string $at) => collect(range(1, 40))->map(fn ($s) => explode(',', $this->greet([], seed: $s, at: $at)['hello'])[0])->unique()->all();

        $this->assertContains('Good morning', $openers('2026-10-28 08:00:00'));
        $this->assertNotContains('Good evening', $openers('2026-10-28 08:00:00'));
        $this->assertContains('Good afternoon', $openers('2026-10-28 14:00:00'));
        $this->assertContains('Good evening', $openers('2026-10-28 20:00:00'));
    }

    public function test_it_varies_and_can_mention_a_running_streak(): void
    {
        $rows = [$this->row('read-more'), $this->row('self-care')];
        $lines = collect(range(1, 80))->map(fn ($s) => $this->greet($rows, ['current' => 5, 'best' => 5], $s)['line'])->unique();

        $this->assertGreaterThan(3, $lines->count());                       // random, not a fixed sentence
        $this->assertTrue($lines->contains('Keep your 5-day streak alive?')); // the streak line is one of the options
    }

    public function test_the_dashboard_shows_the_greeting_today_but_not_on_past_days(): void
    {
        $this->row('read-more');

        $greeting = $this->get('/')->assertOk()->viewData('greeting');
        $this->assertStringEndsWith(', Ayu', $greeting['hello']);
        $this->get('/')->assertSee('Wednesday, 28 October 2026')->assertSee('greet', false)->assertSee('Avatar of Ayu Lestari', false);

        $this->get('/?date=2026-10-20')->assertOk()->assertSee('Back to today');
    }
}
