<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users (Admin, Staff, Customers)
        $admin = User::create([
            'name' => 'Muhammad Ixmal Alimudin',
            'email' => 'admin@laundryexpress.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Ahmad Yani No. 45, Banjarbaru',
        ]);
        UserProfile::factory()->create([
            'user_id' => $admin->id,
            'notes' => 'Owner & Head Administrator Laundry Express',
        ]);

        $staff = User::create([
            'name' => 'Siti Rahmah (Operator)',
            'email' => 'staff@laundryexpress.id',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'phone' => '089876543210',
            'address' => 'Jl. Panglima Batur No. 12, Banjarbaru',
        ]);
        UserProfile::factory()->create(['user_id' => $staff->id]);

        $customers = User::factory(5)->create(['role' => 'customer']);
        foreach ($customers as $cust) {
            UserProfile::factory()->create(['user_id' => $cust->id]);
        }

        // 2. Seed Service Categories & Items
        $catKiloan = ServiceCategory::create([
            'name' => 'Layanan Kiloan',
            'slug' => 'layanan-kiloan',
            'description' => 'Cuci pakaian harian ditimbang per kilogram',
        ]);

        $catSatuan = ServiceCategory::create([
            'name' => 'Layanan Satuan & Spesialis',
            'slug' => 'layanan-satuan-spesialis',
            'description' => 'Cuci khusus bedcover, jas, gaun, dan karpet per buah',
        ]);

        $catSepatu = ServiceCategory::create([
            'name' => 'Sepatu & Tas',
            'slug' => 'sepatu-tas',
            'description' => 'Deep cleaning dan perawatan sepatu & tas kesayangan',
        ]);

        $itemKiloanReg = ServiceItem::create([
            'service_category_id' => $catKiloan->id,
            'name' => 'Cuci Komplit Reguler (Cuci + Setrika + Harum)',
            'price_per_unit' => 7000,
            'unit_type' => 'kg',
            'estimated_hours' => 24,
        ]);

        $itemKiloanExp = ServiceItem::create([
            'service_category_id' => $catKiloan->id,
            'name' => 'Cuci Komplit Express 6 Jam',
            'price_per_unit' => 12000,
            'unit_type' => 'kg',
            'estimated_hours' => 6,
        ]);

        $itemBedcover = ServiceItem::create([
            'service_category_id' => $catSatuan->id,
            'name' => 'Cuci Bedcover Jumbo',
            'price_per_unit' => 35000,
            'unit_type' => 'pcs',
            'estimated_hours' => 24,
        ]);

        $itemSepatu = ServiceItem::create([
            'service_category_id' => $catSepatu->id,
            'name' => 'Deep Clean Sneakers / Shoes',
            'price_per_unit' => 30000,
            'unit_type' => 'pair',
            'estimated_hours' => 48,
        ]);

        // 3. Seed Racks
        $rackA1 = StorageRack::create(['code' => 'RAK-A1', 'section' => 'Zona Kiloan Selesai', 'capacity' => 10]);
        $rackB1 = StorageRack::create(['code' => 'RAK-B1', 'section' => 'Zona Express & Satuan', 'capacity' => 10]);
        $rackC1 = StorageRack::create(['code' => 'RAK-C1', 'section' => 'Zona Sepatu & Tas', 'capacity' => 5]);

        // 4. Seed Laundry Orders, Items, Payments, Logs, and Reviews
        foreach ($customers as $index => $customer) {
            $order = LaundryOrder::create([
                'order_code' => 'LND-' . date('Ymd') . '-' . sprintf('%03d', $index + 1),
                'customer_id' => $customer->id,
                'rack_id' => ($index % 2 == 0) ? $rackA1->id : $rackB1->id,
                'total_weight_kg' => 4.5,
                'total_amount' => 31500,
                'discount_amount' => 0,
                'final_amount' => 31500,
                'status' => 'ready_for_pickup',
                'pickup_deadline' => now()->addDays(1),
            ]);

            OrderItem::create([
                'laundry_order_id' => $order->id,
                'service_item_id' => $itemKiloanReg->id,
                'qty' => 4,
                'price_snapshot' => 7000,
                'subtotal' => 28000,
                'notes' => 'Pakaian warna dipisah dari putih',
            ]);

            Payment::create([
                'payment_code' => 'PAY-' . strtoupper(Str::random(8)),
                'laundry_order_id' => $order->id,
                'amount' => 31500,
                'payment_method' => 'qris',
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            OrderStatusLog::create([
                'laundry_order_id' => $order->id,
                'staff_id' => $staff->id,
                'previous_status' => 'washing',
                'new_status' => 'ready_for_pickup',
                'notes' => 'Pakaian sudah selesai disetrika dan dikemas di Rak',
            ]);

            CustomerReview::create([
                'laundry_order_id' => $order->id,
                'customer_id' => $customer->id,
                'rating' => 5,
                'comment' => 'Harum sekali cuciannya dan selesai tepat waktu!',
            ]);
        }
    }
}
