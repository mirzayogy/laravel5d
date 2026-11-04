<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\AchievementSeeder;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed([CategorySeeder::class, AchievementSeeder::class]);
    }

    private function newUser(): User
    {
        $user = User::factory()->create();
        $user->profile()->create(['timezone' => 'Asia/Makassar']);
        $this->actingAs($user);

        return $user;
    }

    public function test_new_users_are_redirected_to_onboarding(): void
    {
        $this->newUser();

        $this->get('/')->assertRedirect('/onboarding');
        $this->get('/habits')->assertRedirect('/onboarding');
        $this->get('/onboarding')->assertOk()->assertSee('Pick a few starter habits');
    }

    public function test_finishing_creates_starter_habits_and_reminders(): void
    {
        $user = $this->newUser();

        $this->post('/onboarding', [
            'timezone' => 'Asia/Jakarta',
            'daily_goal' => 4,
            'starters' => ['stay-hydrated|0', 'read-more|0', 'bogus|9'],
            'reminder_time' => '07:30',
        ])->assertRedirect('/');

        $user->refresh();
        $this->assertNotNull($user->profile->onboarded_at);
        $this->assertSame('Asia/Jakarta', $user->profile->timezone);
        $this->assertSame(['Drink water', 'Read'], $user->habits()->orderBy('name')->pluck('name')->all());
        $this->assertDatabaseCount('reminders', 2);
        $this->assertSame('07:30', substr($user->habits()->first()->reminders()->first()->remind_at, 0, 5));

        $this->get('/')->assertOk();
        $this->get('/onboarding')->assertRedirect('/');
    }

    public function test_onboarding_can_be_finished_without_habits_and_validates(): void
    {
        $user = $this->newUser();

        $this->post('/onboarding', ['timezone' => 'Mars/Base', 'daily_goal' => 0])->assertSessionHasErrors(['timezone', 'daily_goal']);
        $this->post('/onboarding', ['timezone' => 'UTC', 'daily_goal' => 3, 'reminder_time' => ''])->assertRedirect('/');

        $this->assertSame(0, $user->habits()->count());
        $this->assertNotNull($user->fresh()->profile->onboarded_at);
    }

    public function test_creating_first_habit_unlocks_achievement(): void
    {
        $user = $this->newUser();
        Achievement::create(['name' => 'Starter', 'criteria' => 'habits:1']);

        $this->post('/onboarding', ['timezone' => 'UTC', 'daily_goal' => 3, 'starters' => ['read-more|0']]);

        $this->assertTrue($user->achievements()->where('name', 'Starter')->exists());
        $this->assertGreaterThan(0, Category::count());
    }

    public function test_starter_habits_are_named_in_the_users_language(): void
    {
        $user = $this->newUser();
        $user->profile->update(['locale' => 'id']);

        $this->post('/onboarding', ['timezone' => 'UTC', 'daily_goal' => 3, 'starters' => ['stay-hydrated|0']]);

        $this->assertSame(['Minum air'], $user->habits()->pluck('name')->all());
    }
}
