<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Lots of demo data: staff, customers with Indonesian names, and a few months of orders with payments.
 * Counts can be changed with config('seeding.customers') and config('seeding.orders').
 */
class DemoDataSeeder extends Seeder
{
    private const FIRST_NAMES = [
        'Siti', 'Budi', 'Dewi', 'Agus', 'Rina', 'Eko', 'Putri', 'Dimas', 'Ayu', 'Rizky',
        'Nur', 'Fajar', 'Indah', 'Hendra', 'Lestari', 'Andi', 'Maya', 'Yoga', 'Intan', 'Bayu',
        'Citra', 'Reza', 'Wulan', 'Arif', 'Sari', 'Ilham', 'Dian', 'Taufik', 'Novi', 'Hasan',
    ];

    private const LAST_NAMES = [
        'Rahma', 'Santoso', 'Lestari', 'Pratama', 'Wijaya', 'Saputra', 'Hidayat', 'Kusuma', 'Nugroho', 'Permata',
        'Ramadhan', 'Setiawan', 'Maulana', 'Utami', 'Firmansyah', 'Anggraini', 'Syahputra', 'Hakim', 'Fitriani', 'Gunawan',
    ];

    private const STREETS = [
        'Jl. A. Yani Km 36', 'Jl. Trikora', 'Jl. Panglima Batur', 'Jl. Pangeran Antasari', 'Jl. Veteran',
        'Jl. Sudimampir', 'Jl. RK Ilir', 'Jl. Lambung Mangkurat', 'Jl. Jenderal Sudirman', 'Jl. Mistar Cokrokusumo',
    ];

    private const ORDER_NOTES = [
        'Tolong cepat ya, terima kasih.', 'Tanpa sedotan plastik.', 'Dibungkus rapi.', 'Pedasnya sedang saja.',
        'Antar ke meja dekat jendela.', 'Pisahkan saus dan sambal.', 'Tidak pakai bawang goreng.',
    ];

    public function run(): void
    {
        $this->createStaff();
        $customers = $this->createCustomers((int) config('seeding.customers', 40));

        $this->createOrders($customers, (int) config('seeding.orders', 1000));
    }

    private function createStaff(): void
    {
        User::factory()->create(['name' => 'Admin', 'email' => 'admin@example.com', 'role' => 'admin']);
        User::factory()->create(['name' => 'Kasir', 'email' => 'cashier@example.com', 'role' => 'cashier']);

        foreach (['Rahmat Kasir', 'Lina Kasir'] as $index => $name) {
            User::factory()->create(['name' => $name, 'email' => 'kasir'.($index + 2).'@example.com', 'role' => 'cashier']);
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function createCustomers(int $count)
    {
        $customers = collect();

        for ($i = 0; $i < $count; $i++) {
            $first = self::FIRST_NAMES[$i % count(self::FIRST_NAMES)];
            $last = self::LAST_NAMES[intdiv($i, 2) % count(self::LAST_NAMES)];

            $user = User::factory()->create([
                'name' => $first.' '.$last,
                'email' => $i === 0 ? 'customer@example.com' : Str::lower($first.'.'.$last).($i + 1).'@example.com',
                'role' => 'customer',
            ]);

            Profile::create([
                'user_id' => $user->id,
                'phone' => '08'.rand(11, 99).rand(1000000, 99999999),
                'address' => self::STREETS[array_rand(self::STREETS)].' No. '.rand(1, 120).', '.(rand(0, 1) ? 'Banjarbaru' : 'Banjarmasin'),
            ]);

            $customers->push($user);
        }

        return $customers;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, User>  $customers
     */
    private function createOrders($customers, int $count): void
    {
        $menus = Menu::with('category')->get();

        if ($menus->isEmpty() || $customers->isEmpty() || $count < 1) {
            return;
        }

        $numbers = [];

        DB::disableQueryLog();
        DB::beginTransaction();

        for ($i = 0; $i < $count; $i++) {
            do {
                $number = 'ORD-'.Str::upper(Str::random(8));
            } while (isset($numbers[$number]));
            $numbers[$number] = true;

            $orderedAt = $this->randomTime();
            $status = $this->statusFor($orderedAt);
            $type = rand(1, 100) <= 65 ? 'dine_in' : 'takeaway';
            $method = $this->weighted(['cash' => 35, 'qris' => 35, 'ewallet' => 20, 'transfer' => 10]);
            [$paymentStatus, $paidAt] = $this->paymentFor($status, $method, $orderedAt);

            $order = new Order;
            $order->forceFill([
                'user_id' => $customers->random()->id,
                'order_number' => $number,
                'status' => $status,
                'order_type' => $type,
                'table_number' => $type === 'dine_in' ? (string) rand(1, 20) : null,
                'payment_method' => $method,
                'payment_status' => $paymentStatus,
                'payment_reference' => $method === 'cash' ? null : ($method === 'qris' ? 'QRIS-'.$number : 'REF-'.Str::upper(Str::random(10))),
                'paid_at' => $paidAt,
                'note' => rand(1, 100) <= 12 ? self::ORDER_NOTES[array_rand(self::ORDER_NOTES)] : null,
                'ordered_at' => $orderedAt,
                'created_at' => $orderedAt,
                'updated_at' => $orderedAt,
            ])->save();

            $rows = [];

            foreach ($menus->random(min($menus->count(), rand(1, 5))) as $menu) {
                $rows[] = [
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'quantity' => $this->weighted([1 => 55, 2 => 30, 3 => 10, 4 => 5]),
                    'unit_price' => $menu->price,
                    'note' => $this->itemNote($menu),
                ];
            }

            OrderItem::insert($rows);
        }

        DB::commit();
    }

    private function randomTime()
    {
        $day = now()->subDays(rand(0, 59))->startOfDay();
        $hour = $this->weighted([8 => 6, 9 => 8, 10 => 8, 11 => 12, 12 => 16, 13 => 14, 14 => 8, 15 => 8, 16 => 10, 17 => 12, 18 => 14, 19 => 14, 20 => 10, 21 => 4]);
        $time = $day->setTime($hour, rand(0, 59), rand(0, 59));

        return $time->greaterThan(now()) ? now()->subMinutes(rand(1, 180)) : $time;
    }

    private function statusFor($orderedAt): string
    {
        if ($orderedAt->lt(now()->subHours(6))) {
            return rand(1, 100) <= 90 ? 'completed' : 'cancelled';
        }

        return $this->weighted(['pending' => 25, 'preparing' => 25, 'completed' => 40, 'cancelled' => 10]);
    }

    /**
     * @return array{0: string, 1: mixed}
     */
    private function paymentFor(string $status, string $method, $orderedAt): array
    {
        $paidAt = $orderedAt->copy()->addMinutes(rand(1, 20));

        return match ($status) {
            'completed' => ['paid', $paidAt],
            'cancelled' => rand(1, 100) <= 30 ? ['refunded', $paidAt] : ['unpaid', null],
            'preparing' => rand(1, 100) <= 85 ? ['paid', $paidAt] : ['unpaid', null],
            default => ($method !== 'cash' && rand(1, 100) <= 50) ? ['paid', $paidAt] : ['unpaid', null],
        };
    }

    private function itemNote(Menu $menu): ?string
    {
        if (rand(1, 100) > 25) {
            return null;
        }

        return match ($menu->category->optionSet()) {
            'drink' => Arr::random(['Less sugar', 'No sugar', 'Less ice', 'No ice']),
            'food' => Arr::random(['Extra spicy', 'Not spicy']),
            default => null,
        };
    }

    /**
     * @param  array<int|string, int>  $weights
     */
    private function weighted(array $weights): int|string
    {
        $pick = rand(1, array_sum($weights));

        foreach ($weights as $value => $weight) {
            $pick -= $weight;

            if ($pick <= 0) {
                return $value;
            }
        }

        return array_key_first($weights);
    }
}
