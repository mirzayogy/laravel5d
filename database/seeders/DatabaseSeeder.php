<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PeriodeSeeder::class);
        $this->call(IndonesianMenuSeeder::class);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Kasir',
            'email' => 'cashier@example.com',
            'role' => 'cashier',
        ]);

        $customers = collect([
            ['Siti Rahma', 'customer@example.com'],
            ['Budi Santoso', 'budi@example.com'],
            ['Dewi Lestari', 'dewi@example.com'],
        ])->map(function (array $data) {
            $user = User::factory()->create(['name' => $data[0], 'email' => $data[1], 'role' => 'customer']);
            Profile::factory()->create(['user_id' => $user->id]);

            return $user;
        });

        $menus = Menu::where('is_available', true)->get();
        $statuses = ['pending', 'preparing', 'completed', 'completed', 'completed', 'cancelled'];

        foreach ($customers as $customer) {
            for ($i = 0; $i < 4; $i++) {
                $order = Order::factory()->create([
                    'user_id' => $customer->id,
                    'status' => $statuses[array_rand($statuses)],
                    'ordered_at' => now()->subDays(rand(0, 6))->subMinutes(rand(0, 600)),
                ]);

                foreach ($menus->random(rand(2, 4)) as $menu) {
                    OrderItem::factory()->create([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'quantity' => rand(1, 3),
                        'unit_price' => $menu->price,
                    ]);
                }
            }
        }
    }
}
