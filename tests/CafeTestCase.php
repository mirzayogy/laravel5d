<?php

namespace Tests;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class CafeTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function makeUser(string $role = 'customer'): User
    {
        return User::factory()->create(['role' => $role]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function makeMenu(array $attributes = []): Menu
    {
        return Menu::factory()->create($attributes);
    }

    /**
     * An order of the customer with one item (quantity 1) for each given menu.
     *
     * @param  list<Menu>  $menus
     */
    protected function makeOrder(User $customer, array $menus = [], string $status = 'pending'): Order
    {
        $order = Order::factory()->create(['user_id' => $customer->id, 'status' => $status]);

        foreach ($menus as $menu) {
            $order->items()->create([
                'menu_id' => $menu->id,
                'quantity' => 1,
                'unit_price' => $menu->price,
            ]);
        }

        return $order;
    }
}
