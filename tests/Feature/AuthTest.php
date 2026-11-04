<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guests_are_sent_to_login(): void
    {
        foreach (['/', '/habits', '/mood', '/journal', '/admin', '/admin/users', '/profile'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_login_page_renders_for_guests(): void
    {
        $this->get('/login')->assertOk()->assertSee('Welcome back!')->assertDontSee('Single sign-on')->assertDontSee('magic link')->assertSee('Forgot password?')->assertSee('Terms of Service');
        $this->get('/register')->assertOk();
    }

    public function test_user_can_log_in_and_out(): void
    {
        $user = User::factory()->create(['email' => 'a@example.com']);
        $user->profile()->create(['onboarded_at' => now()]);

        $this->post('/login', ['email' => 'a@example.com', 'password' => 'password'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_wrong_credentials_are_rejected_and_throttled(): void
    {
        User::factory()->create(['email' => 'a@example.com']);

        $this->post('/login', ['email' => 'a@example.com', 'password' => 'nope'])->assertSessionHasErrors('email');
        $this->assertGuest();

        foreach (range(1, 5) as $i) {
            $this->post('/login', ['email' => 'a@example.com', 'password' => 'nope']);
        }
        $this->post('/login', ['email' => 'a@example.com', 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_validates_and_creates_user_with_profile(): void
    {
        $this->post('/register', [])->assertSessionHasErrors(['name', 'email', 'password']);
        $this->post('/register', ['name' => 'A', 'email' => 'a@example.com', 'password' => 'short', 'password_confirmation' => 'short'])
            ->assertSessionHasErrors('password');

        $this->post('/register', ['name' => 'Ayu', 'email' => 'ayu@example.com', 'password' => 'secret-pass', 'password_confirmation' => 'secret-pass'])
            ->assertRedirect('/onboarding');

        $user = User::firstWhere('email', 'ayu@example.com');
        $this->assertNotNull($user->profile);
        $this->assertNull($user->profile->onboarded_at);
        $this->assertAuthenticatedAs($user);

        $this->post('/logout');
        $this->post('/register', ['name' => 'Dup', 'email' => 'ayu@example.com', 'password' => 'secret-pass', 'password_confirmation' => 'secret-pass'])
            ->assertSessionHasErrors('email');
    }
}
