<?php

namespace Tests\Feature;

use Tests\CafeTestCase;

class OrderStatusTest extends CafeTestCase
{
    public function test_a_cashier_moves_an_order_through_the_statuses(): void
    {
        $cashier = $this->makeUser('cashier');
        $order = $this->makeOrder($this->makeUser(), [$this->makeMenu()]);

        $this->actingAs($cashier)
            ->patch(route('staff.orders.status', $order), ['status' => 'preparing'])
            ->assertSessionHas('success');
        $this->assertSame('preparing', $order->fresh()->status);

        $this->actingAs($cashier)
            ->patch(route('staff.orders.status', $order), ['status' => 'completed'])
            ->assertSessionHas('success');
        $this->assertSame('completed', $order->fresh()->status);
    }

    public function test_an_invalid_status_change_is_rejected(): void
    {
        $cashier = $this->makeUser('cashier');
        $order = $this->makeOrder($this->makeUser(), [$this->makeMenu()]);

        $this->actingAs($cashier)
            ->patch(route('staff.orders.status', $order), ['status' => 'completed'])
            ->assertSessionHas('error');

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_a_completed_order_cannot_change_any_more(): void
    {
        $cashier = $this->makeUser('cashier');
        $order = $this->makeOrder($this->makeUser(), [$this->makeMenu()], 'completed');

        $this->actingAs($cashier)
            ->patch(route('staff.orders.status', $order), ['status' => 'cancelled'])
            ->assertSessionHas('error');

        $this->assertSame('completed', $order->fresh()->status);
    }

    public function test_an_unknown_status_is_a_validation_error(): void
    {
        $order = $this->makeOrder($this->makeUser(), [$this->makeMenu()]);

        $this->actingAs($this->makeUser('cashier'))
            ->patch(route('staff.orders.status', $order), ['status' => 'teleported'])
            ->assertSessionHasErrors('status');
    }

    public function test_a_customer_cannot_change_the_status_through_the_staff_route(): void
    {
        $customer = $this->makeUser();
        $order = $this->makeOrder($customer, [$this->makeMenu()]);

        $this->actingAs($customer)
            ->patch(route('staff.orders.status', $order), ['status' => 'preparing'])
            ->assertForbidden();

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_a_customer_can_cancel_a_pending_order(): void
    {
        $customer = $this->makeUser();
        $order = $this->makeOrder($customer, [$this->makeMenu()]);

        $this->actingAs($customer)
            ->post(route('orders.cancel', $order))
            ->assertSessionHas('success');

        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_a_customer_cannot_cancel_an_order_that_is_being_prepared(): void
    {
        $customer = $this->makeUser();
        $order = $this->makeOrder($customer, [$this->makeMenu()], 'preparing');

        $this->actingAs($customer)
            ->post(route('orders.cancel', $order))
            ->assertSessionHas('error');

        $this->assertSame('preparing', $order->fresh()->status);
    }

    public function test_a_customer_cannot_cancel_the_order_of_someone_else(): void
    {
        $order = $this->makeOrder($this->makeUser(), [$this->makeMenu()]);

        $this->actingAs($this->makeUser())
            ->post(route('orders.cancel', $order))
            ->assertForbidden();

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_only_the_owner_and_staff_can_open_an_order(): void
    {
        $owner = $this->makeUser();
        $order = $this->makeOrder($owner, [$this->makeMenu(['name' => 'Latte'])]);

        $this->actingAs($owner)->get(route('orders.show', $order))->assertOk()->assertSee('Latte');
        $this->actingAs($this->makeUser('cashier'))->get(route('orders.show', $order))->assertOk();
        $this->actingAs($this->makeUser('admin'))->get(route('orders.show', $order))->assertOk();
        $this->actingAs($this->makeUser())->get(route('orders.show', $order))->assertForbidden();
    }

    public function test_the_order_history_shows_only_the_own_orders(): void
    {
        $customer = $this->makeUser();
        $mine = $this->makeOrder($customer, [$this->makeMenu()]);
        $other = $this->makeOrder($this->makeUser(), [$this->makeMenu()]);

        $this->actingAs($customer)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee($mine->order_number)
            ->assertDontSee($other->order_number);
    }

    public function test_the_order_queue_can_be_filtered_by_status(): void
    {
        $customer = $this->makeUser();
        $pending = $this->makeOrder($customer, [$this->makeMenu()], 'pending');
        $done = $this->makeOrder($customer, [$this->makeMenu()], 'completed');

        $this->actingAs($this->makeUser('cashier'))
            ->get(route('staff.orders.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee($pending->order_number)
            ->assertDontSee($done->order_number);
    }
}
