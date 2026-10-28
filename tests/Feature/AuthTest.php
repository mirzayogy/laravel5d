<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\CafeTestCase;

class AuthTest extends CafeTestCase
{
    public function test_a_visitor_can_register_as_a_customer(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Yusuf',
            'email' => 'yusuf@example.com',
            'password' => 'secret-pass',
            'password_confirmation' => 'secret-pass',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'yusuf@example.com', 'role' => 'customer']);

        $user = User::where('email', 'yusuf@example.com')->first();
        $this->assertDatabaseHas('profiles', ['user_id' => $user->id]);
    }

    public function test_registration_needs_a_confirmed_password(): void
    {
        $this->post(route('register'), [
            'name' => 'Yusuf',
            'email' => 'yusuf@example.com',
            'password' => 'secret-pass',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_a_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_login_fails_with_a_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get(route('orders.index'))->assertRedirect(route('login'));
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }

    public function test_roles_limit_the_staff_and_admin_pages(): void
    {
        $customer = $this->makeUser('customer');
        $cashier = $this->makeUser('cashier');
        $admin = $this->makeUser('admin');

        $this->actingAs($customer)->get(route('staff.orders.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.menus.index'))->assertForbidden();

        $this->actingAs($cashier)->get(route('staff.orders.index'))->assertOk();
        $this->actingAs($cashier)->get(route('admin.menus.index'))->assertForbidden();

        $this->actingAs($admin)->get(route('staff.orders.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.menus.index'))->assertOk();
    }
}
