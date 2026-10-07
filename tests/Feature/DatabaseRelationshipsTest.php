<?php

namespace Tests\Feature;

use App\Models\CustomerReview;
use App\Models\LaundryOrder;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\ServiceCategory;
use App\Models\ServiceItem;
use App\Models\StorageRack;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_verifies_one_to_one_user_and_user_profile_relationship()
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->profile->is($profile));
        $this->assertTrue($profile->user->is($user));
    }

    public function test_it_verifies_one_to_many_service_category_and_service_items_relationship()
    {
        $category = ServiceCategory::factory()->create();
        $item = ServiceItem::factory()->create(['service_category_id' => $category->id]);

        $this->assertCount(1, $category->serviceItems);
        $this->assertTrue($item->serviceCategory->is($category));
    }

    public function test_it_verifies_one_to_many_customer_user_and_laundry_orders_relationship()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = LaundryOrder::factory()->create(['customer_id' => $customer->id]);

        $this->assertCount(1, $customer->laundryOrders);
        $this->assertTrue($order->customer->is($customer));
    }

    public function test_it_verifies_one_to_many_storage_rack_and_laundry_orders_relationship()
    {
        $rack = StorageRack::factory()->create();
        $order1 = LaundryOrder::factory()->create(['rack_id' => $rack->id]);
        $order2 = LaundryOrder::factory()->create(['rack_id' => $rack->id]);

        $this->assertCount(2, $rack->laundryOrders);
        $this->assertTrue($order1->rack->is($rack));
        $this->assertTrue($order2->rack->is($rack));
    }

    public function test_it_verifies_many_to_many_laundry_order_and_service_items_relationship_with_pivot_data()
    {
        $order = LaundryOrder::factory()->create();
        $service = ServiceItem::factory()->create(['price_per_unit' => 10000]);

        $order->serviceItems()->attach($service->id, [
            'qty' => 3,
            'price_snapshot' => 10000,
            'subtotal' => 30000,
            'notes' => 'Pakaian putih dipisah',
        ]);

        $this->assertTrue($order->serviceItems->contains($service));
        $this->assertEquals(3, $order->serviceItems->first()->pivot->qty);
        $this->assertEquals(30000, $order->serviceItems->first()->pivot->subtotal);
        $this->assertTrue($service->laundryOrders->contains($order));
    }

    public function test_it_verifies_one_to_many_laundry_order_and_payments_relationship()
    {
        $order = LaundryOrder::factory()->create();
        $payment = Payment::factory()->create(['laundry_order_id' => $order->id]);

        $this->assertCount(1, $order->payments);
        $this->assertTrue($payment->laundryOrder->is($order));
    }

    public function test_it_verifies_has_many_through_customer_user_to_payments_relationship()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = LaundryOrder::factory()->create(['customer_id' => $customer->id]);
        $payment = Payment::factory()->create(['laundry_order_id' => $order->id]);

        $this->assertCount(1, $customer->payments);
        $this->assertTrue($customer->payments->first()->is($payment));
    }

    public function test_it_verifies_has_many_through_service_category_to_order_items_relationship()
    {
        $category = ServiceCategory::factory()->create();
        $service = ServiceItem::factory()->create(['service_category_id' => $category->id]);
        $orderItem = OrderItem::factory()->create(['service_item_id' => $service->id]);

        $this->assertCount(1, $category->orderItems);
        $this->assertTrue($category->orderItems->first()->is($orderItem));
    }

    public function test_it_verifies_one_to_many_laundry_order_and_status_logs_relationship()
    {
        $order = LaundryOrder::factory()->create();
        $staff = User::factory()->create(['role' => 'staff']);
        $log = OrderStatusLog::factory()->create([
            'laundry_order_id' => $order->id,
            'staff_id' => $staff->id,
        ]);

        $this->assertCount(1, $order->statusLogs);
        $this->assertTrue($log->laundryOrder->is($order));
        $this->assertTrue($log->staff->is($staff));
    }

    public function test_it_verifies_one_to_one_laundry_order_and_customer_review_relationship()
    {
        $order = LaundryOrder::factory()->create();
        $customer = User::factory()->create(['role' => 'customer']);
        $review = CustomerReview::factory()->create([
            'laundry_order_id' => $order->id,
            'customer_id' => $customer->id,
        ]);

        $this->assertTrue($order->review->is($review));
        $this->assertTrue($review->laundryOrder->is($order));
        $this->assertTrue($review->customer->is($customer));
    }
}
