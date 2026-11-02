<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PeriodeSeeder::class);
        $this->call(CategorySeeder::class);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Cashier',
            'email' => 'cashier@example.com',
            'role' => 'cashier',
        ]);

        $customers = User::factory(3)
            ->create(['role' => 'customer'])
            ->each(fn (User $user) => Profile::factory()->create(['user_id' => $user->id]));

        $tags = collect(['Spicy', 'Vegetarian', 'Cold', 'Hot', 'Best Seller'])
            ->map(fn (string $name) => Tag::create(['name' => $name]));

        $menus = collect();

        foreach (Category::all() as $category) {
            $created = Menu::factory(2)->create(['category_id' => $category->id]);

            foreach ($created as $menu) {
                $menu->tags()->attach($tags->random(2)->pluck('id'));
                $menus->push($menu);
            }
        }

        $statuses = ['pending', 'preparing', 'completed', 'cancelled'];

        foreach ($customers as $customer) {
            for ($i = 0; $i < 2; $i++) {
                $order = Order::factory()->create([
                    'user_id' => $customer->id,
                    'status' => $statuses[array_rand($statuses)],
                ]);

                foreach ($menus->random(3) as $menu) {
                    OrderItem::factory()->create([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'quantity' => fake()->numberBetween(1, 3),
                        'unit_price' => $menu->price,
                    ]);
                }
            }
        }
    }
}
