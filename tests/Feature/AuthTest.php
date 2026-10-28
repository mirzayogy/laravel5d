<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\FlagboardTestCase;

class AuthTest extends FlagboardTestCase
{
    public function test_a_visitor_can_register_as_a_player(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Dhea',
            'email' => 'dhea@example.com',
            'password' => 'secret-pass',
            'password_confirmation' => 'secret-pass',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'dhea@example.com', 'role' => 'player']);

        $user = User::where('email', 'dhea@example.com')->first();
        $this->assertDatabaseHas('profiles', ['user_id' => $user->id]);
    }

    public function test_registration_needs_a_confirmed_password(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Dhea',
            'email' => 'dhea@example.com',
            'password' => 'secret-pass',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
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
        $this->get(route('team.show'))->assertRedirect(route('login'));
    }

    public function test_only_admins_can_open_the_admin_pages(): void
    {
        $this->get(route('admin.competitions.index'))->assertRedirect(route('login'));

        $this->actingAs($this->makePlayer())
            ->get(route('admin.competitions.index'))
            ->assertForbidden();

        $this->actingAs($this->makeAdmin())
            ->get(route('admin.competitions.index'))
            ->assertOk();
    }
}
