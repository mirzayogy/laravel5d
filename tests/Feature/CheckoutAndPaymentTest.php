<?php

namespace Tests\Feature;

use App\Models\Order;
use Tests\CafeTestCase;

class CheckoutAndPaymentTest extends CafeTestCase
{
    public function test_checkout_saves_order_type_table_payment_method_and_item_preference(): void
    {
        $customer = $this->makeUser('customer');
        $menu = $this->makeMenu(['price' => 20000]);

        $this->actingAs($customer)->post(route('orders.store'), [
            'items' => [$menu->id => 2],
            'options' => [$menu->id => 'Less sugar'],
            'order_type' => 'dine_in',
            'table_number' => '7',
            'payment_method' => 'qris',
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame('dine_in', $order->order_type);
        $this->assertSame('7', $order->table_number);
        $this->assertSame('qris', $order->payment_method);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('QRIS-'.$order->order_number, $order->payment_reference);
        $this->assertSame('Less sugar', $order->items()->first()->note);
    }

    public function test_takeaway_orders_have_no_table_number(): void
    {
        $customer = $this->makeUser('customer');
        $menu = $this->makeMenu();

        $this->actingAs($customer)->post(route('orders.store'), [
            'items' => [$menu->id => 1],
            'order_type' => 'takeaway',
            'table_number' => '9',
        ]);

        $this->assertNull(Order::firstOrFail()->table_number);
    }

    public function test_invalid_order_type_and_payment_method_are_rejected(): void
    {
        $customer = $this->makeUser('customer');
        $menu = $this->makeMenu();

        $this->actingAs($customer)->post(route('orders.store'), [
            'items' => [$menu->id => 1],
            'order_type' => 'drive_thru',
            'payment_method' => 'bitcoin',
        ])->assertSessionHasErrors(['order_type', 'payment_method']);

        $this->assertSame(0, Order::count());
    }

    public function test_default_payment_method_is_cash_and_dine_in(): void
    {
        $customer = $this->makeUser('customer');
        $menu = $this->makeMenu();

        $this->actingAs($customer)->post(route('orders.store'), ['items' => [$menu->id => 1]]);

        $order = Order::firstOrFail();
        $this->assertSame('cash', $order->payment_method);
        $this->assertSame('dine_in', $order->order_type);
    }

    public function test_customer_can_open_the_payment_page_for_each_online_method(): void
    {
        $customer = $this->makeUser('customer');
        $order = $this->makeOrder($customer, [$this->makeMenu()]);

        foreach (['qris', 'transfer', 'ewallet', 'cash'] as $method) {
            $this->actingAs($customer)
                ->post(route('orders.payment.method', $order), ['payment_method' => $method])
                ->assertRedirect(route('orders.payment', $order));

            $this->actingAs($customer)->get(route('orders.payment', $order))->assertOk()->assertSee('This is a simulation');
        }
    }

    public function test_transfer_creates_a_virtual_account_number(): void
    {
        $customer = $this->makeUser('customer');
        $order = $this->makeOrder($customer, [$this->makeMenu()]);

        $this->actingAs($customer)->post(route('orders.payment.method', $order), ['payment_method' => 'transfer']);

        $this->assertMatchesRegularExpression('/^8808\d{12}$/', $order->fresh()->payment_reference);
    }

    public function test_customer_can_simulate_an_online_payment(): void
    {
        $customer = $this->makeUser('customer');
        $order = $this->makeOrder($customer, [$this->makeMenu()]);
        $this->actingAs($customer)->post(route('orders.payment.method', $order), ['payment_method' => 'qris']);

        $this->actingAs($customer)->post(route('orders.payment.confirm', $order))
            ->assertRedirect(route('orders.show', $order))
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertNotNull($order->paid_at);
    }

    public function test_cash_cannot_be_paid_online_and_cancelled_orders_cannot_be_paid(): void
    {
        $customer = $this->makeUser('customer');
        $menu = $this->makeMenu();
        $cash = $this->makeOrder($customer, [$menu]);
        $cash->update(['payment_method' => 'cash']);

        $this->actingAs($customer)->post(route('orders.payment.confirm', $cash))->assertSessionHas('error');
        $this->assertSame('unpaid', $cash->fresh()->payment_status);

        $cancelled = $this->makeOrder($customer, [$menu], 'cancelled');
        $this->actingAs($customer)->get(route('orders.payment', $cancelled))->assertRedirect(route('orders.show', $cancelled));
    }

    public function test_a_customer_cannot_pay_someone_elses_order(): void
    {
        $order = $this->makeOrder($this->makeUser('customer'), [$this->makeMenu()]);

        $this->actingAs($this->makeUser('customer'))->get(route('orders.payment', $order))->assertForbidden();
        $this->actingAs($this->makeUser('customer'))->post(route('orders.payment.confirm', $order))->assertForbidden();
    }

    public function test_cashier_confirms_cash_and_sees_the_change(): void
    {
        $cashier = $this->makeUser('cashier');
        $order = $this->makeOrder($this->makeUser('customer'), [$this->makeMenu(['price' => 18000])]);
        $order->update(['payment_method' => 'cash']);

        $this->actingAs($cashier)->post(route('staff.orders.pay', $order), ['cash_received' => 20000])
            ->assertSessionHas('success', fn ($message) => str_contains($message, 'Change: Rp 2.000'));

        $this->assertSame('paid', $order->fresh()->payment_status);
    }

    public function test_cashier_cannot_accept_too_little_cash(): void
    {
        $order = $this->makeOrder($this->makeUser('customer'), [$this->makeMenu(['price' => 18000])]);
        $order->update(['payment_method' => 'cash']);

        $this->actingAs($this->makeUser('cashier'))->post(route('staff.orders.pay', $order), ['cash_received' => 5000])
            ->assertSessionHas('error');

        $this->assertSame('unpaid', $order->fresh()->payment_status);
    }

    public function test_customers_cannot_use_the_cashier_payment_route(): void
    {
        $customer = $this->makeUser('customer');
        $order = $this->makeOrder($customer, [$this->makeMenu()]);

        $this->actingAs($customer)->post(route('staff.orders.pay', $order))->assertForbidden();
    }

    public function test_receipt_is_visible_to_the_owner_and_staff_only(): void
    {
        $customer = $this->makeUser('customer');
        $order = $this->makeOrder($customer, [$this->makeMenu(['name' => 'Kopi Tubruk'])]);

        $this->actingAs($customer)->get(route('orders.receipt', $order))->assertOk()->assertSee('Kopi Tubruk')->assertSee($order->order_number);
        $this->actingAs($this->makeUser('cashier'))->get(route('orders.receipt', $order))->assertOk();
        $this->actingAs($this->makeUser('customer'))->get(route('orders.receipt', $order))->assertForbidden();
    }

    public function test_order_state_endpoint_returns_status_for_polling(): void
    {
        $customer = $this->makeUser('customer');
        $order = $this->makeOrder($customer, [$this->makeMenu()], 'preparing');

        $this->actingAs($customer)->getJson(route('orders.state', $order))
            ->assertOk()
            ->assertJson(['status' => 'preparing', 'payment_status' => 'unpaid']);

        $this->actingAs($this->makeUser('customer'))->getJson(route('orders.state', $order))->assertForbidden();
    }

    public function test_staff_poll_counts_new_orders(): void
    {
        $customer = $this->makeUser('customer');
        $first = $this->makeOrder($customer, [$this->makeMenu()]);
        $this->makeOrder($customer, [$this->makeMenu()]);

        $this->actingAs($this->makeUser('cashier'))->getJson(route('staff.orders.poll', ['after' => $first->id]))
            ->assertOk()
            ->assertJson(['new_orders' => 1]);
    }

    public function test_staff_queue_can_filter_by_payment_and_search(): void
    {
        $cashier = $this->makeUser('cashier');
        $siti = $this->makeUser('customer');
        $siti->update(['name' => 'Siti Rahma']);
        $paid = $this->makeOrder($siti, [$this->makeMenu()]);
        $paid->update(['payment_status' => 'paid']);
        $unpaid = $this->makeOrder($this->makeUser('customer'), [$this->makeMenu()]);

        $this->actingAs($cashier)->get(route('staff.orders.index', ['payment' => 'paid']))
            ->assertSee($paid->order_number)->assertDontSee($unpaid->order_number);

        $this->actingAs($cashier)->get(route('staff.orders.index', ['q' => 'Siti']))
            ->assertSee($paid->order_number)->assertDontSee($unpaid->order_number);
    }
}
