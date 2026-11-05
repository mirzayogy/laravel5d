<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TokoOnlineSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori
        $elektronik = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik', 'description' => 'Perangkat elektronik']);
        $fashion    = Category::create(['name' => 'Fashion', 'slug' => 'fashion', 'description' => 'Pakaian dan aksesoris']);
        $makanan    = Category::create(['name' => 'Makanan', 'slug' => 'makanan', 'description' => 'Makanan dan minuman']);

        // Produk
        $mouse   = Product::create(['category_id' => $elektronik->id, 'name' => 'Mouse Wireless', 'sku' => 'ELK-001', 'price' => 85000, 'stock' => 50]);
        $headset = Product::create(['category_id' => $elektronik->id, 'name' => 'Headset Gaming', 'sku' => 'ELK-002', 'price' => 250000, 'stock' => 20]);
        $kaos    = Product::create(['category_id' => $fashion->id, 'name' => 'Kaos Polos', 'sku' => 'FSH-001', 'price' => 65000, 'stock' => 100]);
        $jaket   = Product::create(['category_id' => $fashion->id, 'name' => 'Jaket Hoodie', 'sku' => 'FSH-002', 'price' => 175000, 'stock' => 35]);
        $kopi    = Product::create(['category_id' => $makanan->id, 'name' => 'Kopi Bubuk 250g', 'sku' => 'MKN-001', 'price' => 45000, 'stock' => 80]);
        Product::create(['category_id' => $makanan->id, 'name' => 'Teh Hijau 100g', 'sku' => 'MKN-002', 'price' => 30000, 'stock' => 60]);

        // User + profil (one-to-one)
        $budi = User::create(['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'password' => Hash::make('password')]);
        $sari = User::create(['name' => 'Sari Wulandari', 'email' => 'sari@example.com', 'password' => Hash::make('password')]);

        UserProfile::create(['user_id' => $budi->id, 'phone' => '081234567890', 'address' => 'Jl. Ahmad Yani Km 5', 'city' => 'Banjarmasin']);
        UserProfile::create(['user_id' => $sari->id, 'phone' => '081298765432', 'address' => 'Jl. Pangeran Antasari No. 10', 'city' => 'Banjarbaru']);

        // Pesanan + item (one-to-many dan many-to-many)
        $this->buatPesanan($budi, 'INV-0001', 'paid', [
            [$mouse, 1],
            [$kopi, 2],
        ]);

        $this->buatPesanan($sari, 'INV-0002', 'pending', [
            [$kaos, 2],
            [$jaket, 1],
            [$headset, 1],
        ]);
    }

    /**
     * @param array<int, array{0: Product, 1: int}> $baris
     */
    private function buatPesanan(User $user, string $nomor, string $status, array $baris): void
    {
        $order = Order::create([
            'user_id'      => $user->id,
            'order_number' => $nomor,
            'status'       => $status,
            'total_amount' => 0,
            'ordered_at'   => now(),
        ]);

        $total = 0;

        foreach ($baris as [$produk, $qty]) {
            $subtotal = $produk->price * $qty;
            $total += $subtotal;

            $order->items()->create([
                'product_id' => $produk->id,
                'quantity'   => $qty,
                'unit_price' => $produk->price,
                'subtotal'   => $subtotal,
            ]);
        }

        $order->update(['total_amount' => $total]);
    }
}
