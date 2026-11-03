<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\FlagboardTestCase;

class AccountTest extends FlagboardTestCase
{
    public function test_registering_sends_a_verification_email(): void
    {
        Notification::fake();

        $this->post(route('register'), [
            'name' => 'New Player',
            'email' => 'new@example.com',
            'password' => 'secret-pass',
            'password_confirmation' => 'secret-pass',
        ]);

        Notification::assertSentTo(User::where('email', 'new@example.com')->first(), VerifyEmail::class);
    }

    public function test_an_unverified_user_cannot_submit_flags(): void
    {
        $competition = $this->makeCompetition();
        $user = User::factory()->unverified()->create();
        $team = $this->makePlayer()->team;
        $user->update(['team_id' => $team->id]);
        $this->register($team, $competition);
        $challenge = $this->makeChallenge($competition, 'FLAG{x}');

        $this->actingAs($user)
            ->post(route('challenges.submit', $challenge), ['flag' => 'FLAG{x}'])
            ->assertRedirect(route('verification.notice'));

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_the_verification_link_verifies_the_email(): void
    {
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), ['id' => $user->id, 'hash' => sha1($user->email)]);

        $this->actingAs($user)->get($url)->assertRedirect(route('dashboard'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_the_verification_email_can_be_sent_again(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->post(route('verification.send'))->assertSessionHas('status');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_a_reset_link_is_sent_without_revealing_which_emails_exist(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status');
        $this->post(route('password.email'), ['email' => 'nobody@example.com'])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
        Notification::assertCount(1);
    }

    public function test_the_password_can_be_reset_with_a_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('brand-new-pass', $user->fresh()->password));
    }

    public function test_an_invalid_reset_token_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.update'), [
            'token' => 'not-a-token',
            'email' => $user->email,
            'password' => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ])->assertSessionHasErrors('email');
    }

    public function test_a_user_can_change_the_profile_and_the_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Renamed',
            'bio' => 'Hello',
            'institution' => 'UNISKA MAB',
            'github_url' => 'https://github.com/renamed',
        ])->assertRedirect(route('profile.edit'));

        $this->assertSame('Renamed', $user->fresh()->name);
        $this->assertDatabaseHas('profiles', ['user_id' => $user->id, 'institution' => 'UNISKA MAB']);

        $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'wrong',
            'password' => 'another-pass',
            'password_confirmation' => 'another-pass',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'another-pass',
            'password_confirmation' => 'another-pass',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('another-pass', $user->fresh()->password));
    }
}
