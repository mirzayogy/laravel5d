<?php

namespace Tests\Feature;

use Tests\CafeTestCase;

class ReportTest extends CafeTestCase
{
    public function test_only_admin_can_see_reports(): void
    {
        $this->get(route('admin.reports.index'))->assertRedirect(route('login'));
        $this->actingAs($this->makeUser('customer'))->get(route('admin.reports.index'))->assertForbidden();
        $this->actingAs($this->makeUser('cashier'))->get(route('admin.reports.export'))->assertForbidden();
        $this->actingAs($this->makeUser('admin'))->get(route('admin.reports.index'))->assertOk();
    }

    public function test_empty_period_shows_an_empty_state(): void
    {
        $this->actingAs($this->makeUser('admin'))->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('No completed orders in this period');
    }

    public function test_report_counts_only_completed_orders_as_revenue(): void
    {
        $customer = $this->makeUser('customer');
        $menu = $this->makeMenu(['name' => 'Kopi Tubruk', 'price' => 10000]);
        $done = $this->makeOrder($customer, [$menu], 'completed');
        $done->update(['payment_method' => 'qris', 'order_type' => 'takeaway']);
        $this->makeOrder($customer, [$menu], 'pending');
        $this->makeOrder($customer, [$menu], 'cancelled');

        $this->actingAs($this->makeUser('admin'))->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('Rp 10.000')
            ->assertSee('Kopi Tubruk')
            ->assertSee('QRIS')
            ->assertSee('Takeaway');
    }

    public function test_date_range_is_validated(): void
    {
        $this->actingAs($this->makeUser('admin'))
            ->get(route('admin.reports.index', ['from' => '2026-05-10', 'to' => '2026-05-01']))
            ->assertSessionHasErrors('to');
    }

    public function test_csv_export_lists_orders_of_the_range(): void
    {
        $customer = $this->makeUser('customer');
        $order = $this->makeOrder($customer, [$this->makeMenu(['price' => 12000])], 'completed');

        $response = $this->actingAs($this->makeUser('admin'))->get(route('admin.reports.export'));

        $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $csv = $response->streamedContent();
        $this->assertStringContainsString('Order,Date,Customer', $csv);
        $this->assertStringContainsString($order->order_number, $csv);
        $this->assertStringContainsString('12000', $csv);
    }
}
