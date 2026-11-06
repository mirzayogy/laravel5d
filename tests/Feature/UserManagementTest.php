<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\CafeTestCase;

class UserManagementTest extends CafeTestCase
{
    public function test_only_admin_can_open_user_pages(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));

        foreach (['customer', 'cashier'] as $role) {
            $this->actingAs($this->makeUser($role))->get(route('admin.users.index'))->assertForbidden();
        }

        $this->actingAs($this->makeUser('admin'))->get(route('admin.users.index'))->assertOk();
    }

    public function test_admin_can_search_and_filter_users(): void
    {
        $admin = $this->makeUser('admin');
        User::factory()->create(['name' => 'Siti Rahma', 'role' => 'customer']);
        User::factory()->create(['name' => 'Budi Kasir', 'role' => 'cashier']);

        $this->actingAs($admin)->get(route('admin.users.index', ['q' => 'Siti']))
            ->assertSee('Siti Rahma')->assertDontSee('Budi Kasir');

        $this->actingAs($admin)->get(route('admin.users.index', ['role' => 'cashier']))
            ->assertSee('Budi Kasir')->assertDontSee('Siti Rahma');

        $this->actingAs($admin)->get(route('admin.users.index', ['q' => 'nobody-here']))
            ->assertOk()->assertSee('No user found');
    }

    public function test_admin_can_create_a_user_with_profile(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Dewi Lestari',
            'email' => 'dewi@example.com',
            'role' => 'cashier',
            'password' => 'secret123',
            'phone' => '0812345678',
        ])->assertRedirect(route('admin.users.index'))->assertSessionHas('success');

        $user = User::where('email', 'dewi@example.com')->firstOrFail();
        $this->assertSame('cashier', $user->role);
        $this->assertSame('0812345678', $user->profile->phone);
    }

    public function test_create_user_validates_input(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'role' => 'owner',
            'password' => 'short',
        ])->assertSessionHasErrors(['name', 'email', 'role', 'password']);
    }

    public function test_admin_can_update_a_user_and_keep_password_when_blank(): void
    {
        $admin = $this->makeUser('admin');
        $user = $this->makeUser('customer');
        $oldHash = $user->password;

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'New Name',
            'email' => $user->email,
            'role' => 'cashier',
            'password' => '',
        ])->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('cashier', $user->role);
        $this->assertSame($oldHash, $user->password);
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'customer',
        ])->assertSessionHasErrors('role');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_admin_can_delete_a_user_without_orders(): void
    {
        $admin = $this->makeUser('admin');
        $user = $this->makeUser('customer');

        $this->actingAs($admin)->delete(route('admin.users.destroy', $user))->assertSessionHas('success');

        $this->assertModelMissing($user);
    }

    public function test_user_with_orders_and_own_account_cannot_be_deleted(): void
    {
        $admin = $this->makeUser('admin');
        $customer = $this->makeUser('customer');
        $this->makeOrder($customer, [$this->makeMenu()]);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $customer))->assertSessionHas('error');
        $this->assertModelExists($customer);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertSessionHas('error');
        $this->assertModelExists($admin);
    }
}
