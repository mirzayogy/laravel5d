<?php

namespace Tests\Feature;

use App\Models\Order;
use Tests\CafeTestCase;

class OrderPlacementTest extends CafeTestCase
{
    public function test_a_customer_can_place_an_order_with_several_menus(): void
    {
        $customer = $this->makeUser();
        $latte = $this->makeMenu(['name' => 'Latte', 'price' => 25000]);
        $fries = $this->makeMenu(['name' => 'Fries', 'price' => 18000]);

        $response = $this->actingAs($customer)->post(route('orders.store'), [
            'items' => [$latte->id => 2, $fries->id => 1],
            'note' => 'Less sugar',
        ]);

        $order = Order::first();
        $response->assertRedirect(route('orders.show', $order));

        $this->assertSame($customer->id, $order->user_id);
        $this->assertSame('pending', $order->status);
        $this->assertSame('Less sugar', $order->note);
        $this->assertStringStartsWith('ORD-', $order->order_number);
        $this->assertSame(2, $order->items()->count());
        $this->assertSame(68000, $order->total());
    }

    public function test_menus_with_quantity_zero_are_ignored(): void
    {
        $customer = $this->makeUser();
        $latte = $this->makeMenu();
        $fries = $this->makeMenu();

        $this->actingAs($customer)->post(route('orders.store'), [
            'items' => [$latte->id => 0, $fries->id => 3],
        ]);

        $order = Order::first();
        $this->assertSame(1, $order->items()->count());
        $this->assertSame($fries->id, $order->items()->first()->menu_id);
    }

    public function test_the_item_price_is_copied_and_does_not_change_with_the_menu_price(): void
    {
        $customer = $this->makeUser();
        $menu = $this->makeMenu(['price' => 20000]);

        $this->actingAs($customer)->post(route('orders.store'), ['items' => [$menu->id => 2]]);

        $menu->update(['price' => 99000]);

        $order = Order::first();
        $this->assertSame(20000, $order->items()->first()->unit_price);
        $this->assertSame(40000, $order->total());
    }

    public function test_an_order_needs_at_least_one_menu(): void
    {
        $customer = $this->makeUser();
        $menu = $this->makeMenu();

        $this->actingAs($customer)
            ->post(route('orders.store'), ['items' => [$menu->id => 0]])
            ->assertSessionHasErrors('items');

        $this->assertSame(0, Order::count());
    }

    public function test_an_unavailable_menu_cannot_be_ordered(): void
    {
        $customer = $this->makeUser();
        $menu = $this->makeMenu(['name' => 'Seasonal Cake', 'is_available' => false]);

        $this->actingAs($customer)
            ->post(route('orders.store'), ['items' => [$menu->id => 1]])
            ->assertSessionHasErrors('items');

        $this->assertSame(0, Order::count());
    }

    public function test_an_unknown_menu_is_rejected(): void
    {
        $customer = $this->makeUser();

        $this->actingAs($customer)
            ->post(route('orders.store'), ['items' => [9999 => 1]])
            ->assertSessionHasErrors('items');

        $this->assertSame(0, Order::count());
    }

    public function test_the_quantity_has_an_upper_limit(): void
    {
        $customer = $this->makeUser();
        $menu = $this->makeMenu();

        $this->actingAs($customer)
            ->post(route('orders.store'), ['items' => [$menu->id => 21]])
            ->assertSessionHasErrors('items.'.$menu->id);
    }

    public function test_a_guest_cannot_order(): void
    {
        $menu = $this->makeMenu();

        $this->post(route('orders.store'), ['items' => [$menu->id => 1]])
            ->assertRedirect(route('login'));

        $this->assertSame(0, Order::count());
    }

    public function test_staff_cannot_place_orders(): void
    {
        $menu = $this->makeMenu();

        $this->actingAs($this->makeUser('cashier'))
            ->post(route('orders.store'), ['items' => [$menu->id => 1]])
            ->assertForbidden();

        $this->assertSame(0, Order::count());
    }
}
