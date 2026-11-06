<?php

namespace Tests\Feature;

use Tests\CafeTestCase;

class DashboardTest extends CafeTestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_customer_with_no_orders_sees_empty_state(): void
    {
        $customer = $this->makeUser('customer');

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('No orders yet');
    }

    public function test_customer_sees_recent_orders_and_favorites(): void
    {
        $customer = $this->makeUser('customer');
        $menu = $this->makeMenu(['name' => 'Kopi Tubruk']);
        $order = $this->makeOrder($customer, [$menu]);

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Kopi Tubruk');
    }

    public function test_staff_and_admin_see_the_dashboard_even_without_orders(): void
    {
        foreach (['cashier', 'admin'] as $role) {
            $this->actingAs($this->makeUser($role))
                ->get(route('dashboard'))
                ->assertOk()
                ->assertSee('Orders today')
                ->assertSee('No orders yet');
        }
    }

    public function test_staff_dashboard_counts_completed_revenue(): void
    {
        $customer = $this->makeUser('customer');
        $menu = $this->makeMenu(['price' => 20000]);
        $this->makeOrder($customer, [$menu], 'completed');
        $this->makeOrder($customer, [$menu], 'pending');

        $this->actingAs($this->makeUser('admin'))
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rp 20.000');
    }
}
