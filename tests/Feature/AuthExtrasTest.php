<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\CafeTestCase;

class AuthExtrasTest extends CafeTestCase
{
    public function test_forgot_password_page_opens_and_sends_a_link(): void
    {
        Notification::fake();
        $user = $this->makeUser('customer');

        $this->get(route('password.request'))->assertOk()->assertSee('Forgot your password?');

        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('success');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_unknown_email_gets_the_same_answer_and_no_mail(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'nobody@example.com'])->assertSessionHas('success');

        Notification::assertNothingSent();
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $user = $this->makeUser('customer');
        $token = Password::createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))->assertOk();

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secret-1',
            'password_confirmation' => 'new-secret-1',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-secret-1', $user->fresh()->password));
    }

    public function test_invalid_token_does_not_reset_the_password(): void
    {
        $user = $this->makeUser('customer');
        $old = $user->password;

        $this->post(route('password.update'), [
            'token' => 'wrong-token',
            'email' => $user->email,
            'password' => 'new-secret-1',
            'password_confirmation' => 'new-secret-1',
        ])->assertSessionHasErrors('email');

        $this->assertSame($old, $user->fresh()->password);
    }

    public function test_registration_sends_a_verification_email(): void
    {
        Notification::fake();

        $this->post(route('register'), [
            'name' => 'Yusuf',
            'email' => 'yusuf@example.com',
            'password' => 'secret-pass',
            'password_confirmation' => 'secret-pass',
        ]);

        Notification::assertSentTo(User::where('email', 'yusuf@example.com')->first(), VerifyEmail::class);
    }

    public function test_unverified_customer_cannot_place_an_order(): void
    {
        $customer = $this->makeUser('customer');
        $customer->forceFill(['email_verified_at' => null])->save();
        $menu = $this->makeMenu();

        $this->actingAs($customer)->post(route('orders.store'), ['items' => [$menu->id => 1]])
            ->assertRedirect(route('verification.notice'));

        $this->assertDatabaseCount('orders', 0);
        $this->actingAs($customer)->get(route('verification.notice'))->assertOk()->assertSee('Verify your email');
    }

    public function test_signed_link_verifies_the_email(): void
    {
        $customer = $this->makeUser('customer');
        $customer->forceFill(['email_verified_at' => null])->save();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $customer->id,
            'hash' => sha1($customer->email),
        ]);

        $this->actingAs($customer)->get($url)->assertRedirect(route('home'));

        $this->assertTrue($customer->fresh()->hasVerifiedEmail());
    }

    public function test_resend_sends_another_verification_email(): void
    {
        Notification::fake();
        $customer = $this->makeUser('customer');
        $customer->forceFill(['email_verified_at' => null])->save();

        $this->actingAs($customer)->post(route('verification.send'))->assertSessionHas('success');

        Notification::assertSentTo($customer, VerifyEmail::class);
    }

    public function test_demo_verification_only_works_in_the_local_environment(): void
    {
        $customer = $this->makeUser('customer');
        $customer->forceFill(['email_verified_at' => null])->save();

        $this->actingAs($customer)->post(route('verification.demo'))->assertNotFound();
        $this->assertFalse($customer->fresh()->hasVerifiedEmail());
    }

    public function test_users_created_by_an_admin_are_already_verified(): void
    {
        $this->actingAs($this->makeUser('admin'))->post(route('admin.users.store'), [
            'name' => 'Staff',
            'email' => 'staff@example.com',
            'role' => 'cashier',
            'password' => 'secret123',
        ]);

        $this->assertTrue(User::where('email', 'staff@example.com')->firstOrFail()->hasVerifiedEmail());
    }

    public function test_language_can_be_switched(): void
    {
        $this->get(route('locale', 'id'))->assertRedirect()->assertSessionHas('locale', 'id');
        $this->get(route('locale', 'en'))->assertRedirect()->assertSessionHas('locale', 'en');
        $this->get(route('locale', 'fr'))->assertNotFound();

        $this->withSession(['locale' => 'id'])->get(route('home'))->assertSee('Kopi, teh, dan makanan favorit Nusantara');
        $this->withSession(['locale' => 'en'])->get(route('home'))->assertSee('Coffee, tea, and favorite Indonesian food');
    }

    public function test_indonesian_interface_translates_labels_and_statuses(): void
    {
        $customer = $this->makeUser('customer');
        $order = $this->makeOrder($customer, [$this->makeMenu()], 'preparing');

        $this->withSession(['locale' => 'id'])->actingAs($customer)->get(route('orders.show', $order))
            ->assertSee('Diproses')
            ->assertSee('Detail pesanan')
            ->assertSee('Belum dibayar');
    }
}
