<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CustomerProfile;
use App\Models\Merchant;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Seed Periode from upstream
        $this->call(PeriodeSeeder::class);

        // 1. Seed Categories
        $this->call(CategorySeeder::class);
        $categories = Category::all();

        // 2. Seed Tags
        $tagsData = [
            ['name' => 'Halal Certified', 'slug' => 'halal', 'color' => '#10B981'],
            ['name' => 'Khas Banjar', 'slug' => 'khas-banjar', 'color' => '#F59E0B'],
            ['name' => 'Best Seller', 'slug' => 'best-seller', 'color' => '#EF4444'],
            ['name' => 'Pedas Nampol', 'slug' => 'pedas', 'color' => '#DC2626'],
            ['name' => 'Paket Hemat', 'slug' => 'paket-hemat', 'color' => '#3B82F6'],
        ];
        $tags = collect();
        foreach ($tagsData as $t) {
            $tags->push(Tag::updateOrCreate(['slug' => $t['slug']], $t));
        }

        // 3. Seed Demo Customer User
        $customerUser = User::updateOrCreate(
            ['email' => 'customer@lokalbites.test'],
            [
                'name' => 'Muhammad Abdi Nur Salam',
                'password' => Hash::make('password'),
            ]
        );

        CustomerProfile::updateOrCreate(
            ['user_id' => $customerUser->id],
            [
                'phone_number' => '081234567890',
                'address' => 'Jl. Brigjend H. Hasan Basry No. 45, Kayu Tangi',
                'city' => 'Banjarmasin',
                'postal_code' => '70123',
                'delivery_notes' => 'Pagar warna hitam, samping minimarket',
            ]
        );

        // 4. Seed Demo Merchant Owners & Merchants
        $merchantsData = [
            [
                'owner_name' => 'Hj. Siti Rahmah',
                'owner_email' => 'siti@warungrahmah.test',
                'store_name' => 'Warung Banjar Acan Hj. Siti',
                'slug' => 'warung-banjar-acan-hj-siti',
                'description' => 'Spesialis Soto Banjar kuah kental susu bebek dan nasi kuning iwak haruan.',
                'address' => 'Jl. Pangeran Samudera No. 12, Banjarmasin Tengah',
                'phone_number' => '082155551234',
                'rating' => 4.90,
            ],
            [
                'owner_name' => 'Ahmad Firdaus',
                'owner_email' => 'firdaus@wadaipermatasari.test',
                'store_name' => 'Wadai Tradisional Permata Sari',
                'slug' => 'wadai-tradisional-permata-sari',
                'description' => 'Pusat aneka kue basah khas Banjar: Bingka bakar kentang, Amparan Tatak, Sari Pengantin.',
                'address' => 'Jl. Veteran No. 88, Banjarmasin Timur',
                'phone_number' => '085288887766',
                'rating' => 4.85,
            ],
        ];

        $merchants = collect();
        foreach ($merchantsData as $m) {
            $owner = User::updateOrCreate(
                ['email' => $m['owner_email']],
                [
                    'name' => $m['owner_name'],
                    'password' => Hash::make('password'),
                ]
            );

            $merchant = Merchant::updateOrCreate(
                ['slug' => $m['slug']],
                [
                    'user_id' => $owner->id,
                    'store_name' => $m['store_name'],
                    'description' => $m['description'],
                    'address' => $m['address'],
                    'phone_number' => $m['phone_number'],
                    'is_open' => true,
                    'rating' => $m['rating'],
                ]
            );

            $merchants->push($merchant);
        }

        // 5. Seed Menus for Merchant 1 (Makanan Khas)
        $m1 = $merchants[0];
        $catFood = $categories->firstWhere('slug', 'makanan-berat');
        $catBeverage = $categories->firstWhere('slug', 'minuman-kopi');

        $menu1 = Menu::updateOrCreate(
            ['slug' => 'soto-banjar-spesial-hj-siti'],
            [
                'merchant_id' => $m1->id,
                'category_id' => $catFood->id,
                'name' => 'Soto Banjar Spesial Telur Bebek',
                'description' => 'Soto ayam kampung gurih dengan kuah kaldu rempah, perkedel kentang, dan telur bebek asin.',
                'price' => 32000,
                'is_available' => true,
                'preparation_time_minutes' => 15,
            ]
        );
        $menu1->tags()->sync([$tags[0]->id, $tags[1]->id, $tags[2]->id]);

        $menu2 = Menu::updateOrCreate(
            ['slug' => 'nasi-kuning-iwak-haruan-masak-habang'],
            [
                'merchant_id' => $m1->id,
                'category_id' => $catFood->id,
                'name' => 'Nasi Kuning Iwak Haruan Masak Habang',
                'description' => 'Nasi kuning wangi daun pandan dengan bumbu merah khas Banjar manis gurih dan serundeng.',
                'price' => 28000,
                'is_available' => true,
                'preparation_time_minutes' => 10,
            ]
        );
        $menu2->tags()->sync([$tags[0]->id, $tags[1]->id, $tags[3]->id]);

        $menu3 = Menu::updateOrCreate(
            ['slug' => 'es-limau-kuit-segar'],
            [
                'merchant_id' => $m1->id,
                'category_id' => $catBeverage->id,
                'name' => 'Es Limau Kuit Segar',
                'description' => 'Jeruk purut limau kuit khas Kalimantan Selatan dengan gula batu alami.',
                'price' => 10000,
                'is_available' => true,
                'preparation_time_minutes' => 5,
            ]
        );
        $menu3->tags()->sync([$tags[0]->id, $tags[4]->id]);

        // Menus for Merchant 2 (Wadai Banjar)
        $m2 = $merchants[1];
        $catWadai = $categories->firstWhere('slug', 'kue-tradisional');

        $menu4 = Menu::updateOrCreate(
            ['slug' => 'bingka-kentang-bakar-original'],
            [
                'merchant_id' => $m2->id,
                'category_id' => $catWadai->id,
                'name' => 'Bingka Kentang Bakar Original (Bunga 6)',
                'description' => 'Kue bingka lembut manis legit khas Banjar dengan aroma gosong arang khas kelapa.',
                'price' => 45000,
                'is_available' => true,
                'preparation_time_minutes' => 20,
            ]
        );
        $menu4->tags()->sync([$tags[0]->id, $tags[1]->id, $tags[2]->id]);

        $menu5 = Menu::updateOrCreate(
            ['slug' => 'amparan-tatak-pisang-loyang'],
            [
                'merchant_id' => $m2->id,
                'category_id' => $catWadai->id,
                'name' => 'Amparan Tatak Pisang Santan Gurih',
                'description' => 'Kue basah lapisan santan gurih di atas dan pisang raja manis di bawah.',
                'price' => 35000,
                'is_available' => true,
                'preparation_time_minutes' => 15,
            ]
        );
        $menu5->tags()->sync([$tags[0]->id, $tags[1]->id]);

        // 6. Seed Orders with Pivot Items (Many-to-Many with Pivot Data)
        $order1 = Order::updateOrCreate(
            ['order_code' => 'LB-20261002-0001'],
            [
                'user_id' => $customerUser->id,
                'merchant_id' => $m1->id,
                'total_amount' => 74000,
                'delivery_fee' => 10000,
                'status' => 'delivered',
                'delivery_address' => 'Jl. Brigjend H. Hasan Basry No. 45, Kayu Tangi, Banjarmasin',
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'notes' => 'Tolong jeruk nipisnya ditambah ya mas',
            ]
        );

        OrderItem::updateOrCreate(
            ['order_id' => $order1->id, 'menu_id' => $menu1->id],
            [
                'quantity' => 2,
                'unit_price' => 32000,
                'subtotal' => 64000,
                'special_notes' => 'Kuah dipisah, banyakin seledri',
            ]
        );

        OrderItem::updateOrCreate(
            ['order_id' => $order1->id, 'menu_id' => $menu3->id],
            [
                'quantity' => 1,
                'unit_price' => 10000,
                'subtotal' => 10000,
                'special_notes' => 'Es batu sedikit saja',
            ]
        );

        // 7. Seed One-to-One Review for Order 1
        Review::updateOrCreate(
            ['order_id' => $order1->id],
            [
                'user_id' => $customerUser->id,
                'merchant_id' => $m1->id,
                'rating' => 5,
                'comment' => 'Soto Banjarnya enak banar! Kuahnya kental gurih, pengantaran juga cepat dan rapi.',
                'merchant_reply' => 'Alhamdulillah, terima kasih banyak kak Abdi! Ditunggu pesanan berikutnya.',
            ]
        );

        // Order 2 (Order from Merchant 2 - In progress)
        $order2 = Order::updateOrCreate(
            ['order_code' => 'LB-20261002-0002'],
            [
                'user_id' => $customerUser->id,
                'merchant_id' => $m2->id,
                'total_amount' => 90000,
                'delivery_fee' => 10000,
                'status' => 'processing',
                'delivery_address' => 'Jl. Brigjend H. Hasan Basry No. 45, Kayu Tangi, Banjarmasin',
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'notes' => 'Untuk sajian rapat jam 15.00',
            ]
        );

        OrderItem::updateOrCreate(
            ['order_id' => $order2->id, 'menu_id' => $menu4->id],
            [
                'quantity' => 2,
                'unit_price' => 45000,
                'subtotal' => 90000,
                'special_notes' => 'Kotak jangan ditumpuk berat ya',
            ]
        );
    }
}
