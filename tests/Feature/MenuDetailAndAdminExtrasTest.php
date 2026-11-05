<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\IndonesianMenuSeeder;
use Tests\CafeTestCase;

class MenuDetailAndAdminExtrasTest extends CafeTestCase
{
    public function test_menu_detail_page_is_public_and_shows_related_menus(): void
    {
        $category = Category::factory()->create(['name' => 'Kopi']);
        $menu = $this->makeMenu(['category_id' => $category->id, 'name' => 'Kopi Tubruk', 'price' => 12000]);
        $this->makeMenu(['category_id' => $category->id, 'name' => 'Americano']);

        $this->get(route('menus.show', $menu))
            ->assertOk()
            ->assertSee('Kopi Tubruk')
            ->assertSee('Rp 12.000')
            ->assertSee('Americano')
            ->assertSee('Log in to order');
    }

    public function test_menu_detail_without_related_menus_shows_empty_state(): void
    {
        $menu = $this->makeMenu();

        $this->get(route('menus.show', $menu))->assertOk()->assertSee('No other menu in this category yet');
    }

    public function test_unknown_menu_returns_custom_404_page(): void
    {
        $this->get('/menu/9999')->assertNotFound()->assertSee('Page not found');
    }

    public function test_admin_can_toggle_menu_availability(): void
    {
        $admin = $this->makeUser('admin');
        $menu = $this->makeMenu(['is_available' => true]);

        $this->actingAs($admin)->patch(route('admin.menus.toggle', $menu))->assertSessionHas('success');
        $this->assertFalse($menu->fresh()->is_available);

        $this->actingAs($admin)->patch(route('admin.menus.toggle', $menu));
        $this->assertTrue($menu->fresh()->is_available);
    }

    public function test_customer_cannot_toggle_menu_availability(): void
    {
        $menu = $this->makeMenu();

        $this->actingAs($this->makeUser('customer'))->patch(route('admin.menus.toggle', $menu))->assertForbidden();
    }

    public function test_admin_menu_page_has_empty_state(): void
    {
        $this->actingAs($this->makeUser('admin'))->get(route('admin.menus.index'))->assertOk()->assertSee('No menu yet');
    }

    public function test_admin_can_delete_only_finished_orders(): void
    {
        $admin = $this->makeUser('admin');
        $customer = $this->makeUser('customer');
        $menu = $this->makeMenu();
        $pending = $this->makeOrder($customer, [$menu], 'pending');
        $done = $this->makeOrder($customer, [$menu], 'completed');

        $this->actingAs($admin)->delete(route('admin.orders.destroy', $pending))->assertSessionHas('error');
        $this->assertModelExists($pending);

        $this->actingAs($admin)->delete(route('admin.orders.destroy', $done))
            ->assertRedirect(route('staff.orders.index'))
            ->assertSessionHas('success');
        $this->assertModelMissing($done);
        $this->assertDatabaseMissing('order_items', ['order_id' => $done->id]);
    }

    public function test_cashier_and_customer_cannot_delete_orders(): void
    {
        $customer = $this->makeUser('customer');
        $order = $this->makeOrder($customer, [$this->makeMenu()], 'completed');

        $this->actingAs($this->makeUser('cashier'))->delete(route('admin.orders.destroy', $order))->assertForbidden();
        $this->actingAs($customer)->delete(route('admin.orders.destroy', $order))->assertForbidden();
        $this->assertModelExists($order);
    }

    public function test_indonesian_menu_seeder_creates_menu_with_sold_out_item(): void
    {
        $this->seed(IndonesianMenuSeeder::class);

        $this->assertSame(6, Category::count());
        $this->assertGreaterThanOrEqual(30, Menu::count());
        $this->assertFalse(Menu::where('name', 'Sate Ayam Madura')->firstOrFail()->is_available);

        $this->get(route('home'))->assertOk()->assertSee('Kopi Susu Gula Aren')->assertSee('Nasi Goreng Kampung');
    }

    public function test_full_seeder_runs_and_creates_demo_orders(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThan(0, Order::count());
    }
}
