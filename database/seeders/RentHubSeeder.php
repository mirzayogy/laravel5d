<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ItemSpecification;
use App\Models\Profile;
use App\Models\Promotion;
use App\Models\Rental;
use App\Models\RentalDetail;
use App\Models\RentalItem;
use App\Models\RentalReturn;
use App\Models\Review;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RentHubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create primary student user & demo user
        $user = User::firstOrCreate(
            ['email' => 'wirayudhapamuji@gmail.com'],
            [
                'name' => 'Wira Yudha Pamuji',
                'password' => Hash::make('password'),
            ]
        );

        $demoUser = User::firstOrCreate(
            ['email' => 'customer@renthub.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Create Profile (One-to-One)
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone_number' => '081234567890',
                'address' => 'Jl. Mistar Cokrokusumo No. 45, Banjarbaru, Kalimantan Selatan',
                'identity_number' => '6372012410010396',
                'driver_license_type' => 'SIM A & SIM C',
                'avatar' => 'avatars/wira.png',
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $demoUser->id],
            [
                'phone_number' => '085799988877',
                'address' => 'Jl. A. Yani Km 33, Banjarbaru',
                'identity_number' => '6372098765432100',
                'driver_license_type' => 'SIM C',
                'avatar' => 'avatars/budi.png',
            ]
        );

        // 3. Create Tags
        $tagsData = [
            ['name' => 'Best Seller', 'slug' => 'best-seller'],
            ['name' => 'Premium', 'slug' => 'premium'],
            ['name' => 'Outdoor', 'slug' => 'outdoor'],
            ['name' => 'Hemat', 'slug' => 'hemat'],
            ['name' => 'New Arrival', 'slug' => 'new-arrival'],
            ['name' => 'Portable', 'slug' => 'portable'],
        ];

        $tags = [];
        foreach ($tagsData as $t) {
            $tags[$t['slug']] = Tag::firstOrCreate(['slug' => $t['slug']], $t);
        }

        // 4. Create Rental Items with Categories & Specifications
        $mobilCat = Category::where('slug', 'mobil')->first();
        $motorCat = Category::where('slug', 'sepeda-motor')->first();
        $kameraCat = Category::where('slug', 'kamera-fotografi')->first();
        $campingCat = Category::where('slug', 'alat-camping-outdoor')->first();
        $audioCat = Category::where('slug', 'sound-system-audio')->first();
        $toolsCat = Category::where('slug', 'alat-konstruksi')->first();

        $items = [
            [
                'category_id' => $mobilCat->id,
                'name' => 'Toyota All New Avanza 2023',
                'slug' => 'toyota-all-new-avanza-2023',
                'brand' => 'Toyota',
                'daily_rate' => 350000,
                'stock_quantity' => 3,
                'status' => 'available',
                'description' => 'Mobil keluarga 7-seater irit bahan bakar, transmisi CVT, kabin luas dan nyaman.',
                'spec' => [
                    'serial_or_plate_number' => 'DA 1396 WB',
                    'year_of_manufacture' => 2023,
                    'color' => 'Putih Metalik',
                    'condition_notes' => 'Mesin halus, servis rutin bengkel resmi, AC dingin.',
                    'technical_specs' => ['engine' => '1500cc Dual VVT-i', 'transmission' => 'CVT', 'fuel' => 'Pertalite / Pertamax'],
                ],
                'tags' => ['best-seller', 'hemat'],
            ],
            [
                'category_id' => $motorCat->id,
                'name' => 'Honda CRF 150L Trail Adventure',
                'slug' => 'honda-crf-150l-trail-adventure',
                'brand' => 'Honda',
                'daily_rate' => 150000,
                'stock_quantity' => 2,
                'status' => 'available',
                'description' => 'Motor trail dual-purpose tangguh siap jelajah alam maupun jalan perkotaan.',
                'spec' => [
                    'serial_or_plate_number' => 'DA 2410 WY',
                    'year_of_manufacture' => 2022,
                    'color' => 'Extreme Red',
                    'condition_notes' => 'Ban pacul siap trabas, suspensi upside-down empuk.',
                    'technical_specs' => ['engine' => '150cc SOHC PGM-FI', 'transmission' => '5-Speed Manual'],
                ],
                'tags' => ['outdoor', 'premium'],
            ],
            [
                'category_id' => $kameraCat->id,
                'name' => 'Sony Alpha A7 IV Mirrorless Body',
                'slug' => 'sony-alpha-a7-iv-mirrorless',
                'brand' => 'Sony',
                'daily_rate' => 300000,
                'stock_quantity' => 2,
                'status' => 'available',
                'description' => 'Kamera hybrid 33MP full-frame perekaman video 4K 60p 10-bit untuk profesional.',
                'spec' => [
                    'serial_or_plate_number' => 'SN-SNYA7IV-98214',
                    'year_of_manufacture' => 2023,
                    'color' => 'Black',
                    'condition_notes' => 'Sensor bersih tanpa jamur, shutter count rendah.',
                    'technical_specs' => ['sensor' => '33MP Full-Frame Exmor R CMOS', 'video' => '4K 60p 10-Bit 4:2:2'],
                ],
                'tags' => ['premium', 'new-arrival'],
            ],
            [
                'category_id' => $campingCat->id,
                'name' => 'Tenda Dome Eiger Guardian 4 Person',
                'slug' => 'tenda-dome-eiger-guardian-4p',
                'brand' => 'Eiger',
                'daily_rate' => 60000,
                'stock_quantity' => 5,
                'status' => 'available',
                'description' => 'Tenda camping waterproof double layer kapasitas 4 orang dengan vestibule luas.',
                'spec' => [
                    'serial_or_plate_number' => 'EGR-TND-0041',
                    'year_of_manufacture' => 2024,
                    'color' => 'Olive Green',
                    'condition_notes' => 'Frame alloy utuh, pasak lengkap, flysheet tanpa bocor.',
                    'technical_specs' => ['capacity' => '4 Persons', 'waterproof' => 'PU 3000mm'],
                ],
                'tags' => ['outdoor', 'best-seller', 'hemat'],
            ],
            [
                'category_id' => $audioCat->id,
                'name' => 'JBL PartyBox 310 Portable Bluetooth Speaker',
                'slug' => 'jbl-partybox-310-portable-speaker',
                'brand' => 'JBL',
                'daily_rate' => 200000,
                'stock_quantity' => 2,
                'status' => 'available',
                'description' => 'Speaker pesta 240W RMS dengan efek lampu dinamis dan baterai hingga 18 jam.',
                'spec' => [
                    'serial_or_plate_number' => 'JBL-PB310-77123',
                    'year_of_manufacture' => 2023,
                    'color' => 'Black',
                    'condition_notes' => 'Suara bass powerful, roda trolley berfungsi lancar.',
                    'technical_specs' => ['power_output' => '240W RMS', 'battery_life' => 'Up to 18 hours'],
                ],
                'tags' => ['portable', 'premium'],
            ],
            [
                'category_id' => $toolsCat->id,
                'name' => 'Makita Cordless Hammer Drill 18V',
                'slug' => 'makita-cordless-hammer-drill-18v',
                'brand' => 'Makita',
                'daily_rate' => 75000,
                'stock_quantity' => 4,
                'status' => 'available',
                'description' => 'Bor baterai brushless 18V 2 baterai + charger cepat untuk beton, besi, dan kayu.',
                'spec' => [
                    'serial_or_plate_number' => 'MKT-DHP484-5542',
                    'year_of_manufacture' => 2023,
                    'color' => 'Teal Blue',
                    'condition_notes' => 'Gearbox prima, torsi maksimal 54Nm.',
                    'technical_specs' => ['battery' => '18V Li-ion LXT', 'max_torque' => '54 Nm'],
                ],
                'tags' => ['portable', 'hemat'],
            ],
        ];

        $createdItems = [];
        foreach ($items as $itemData) {
            $specData = $itemData['spec'];
            $tagSlugs = $itemData['tags'];
            unset($itemData['spec'], $itemData['tags']);

            $item = RentalItem::updateOrCreate(['slug' => $itemData['slug']], $itemData);
            $createdItems[$item->slug] = $item;

            // One-to-One: ItemSpecification
            ItemSpecification::updateOrCreate(
                ['rental_item_id' => $item->id],
                array_merge($specData, ['rental_item_id' => $item->id])
            );

            // Many-to-Many: Attach Tags
            $attachIds = [];
            foreach ($tagSlugs as $slug) {
                if (isset($tags[$slug])) {
                    $attachIds[] = $tags[$slug]->id;
                }
            }
            $item->tags()->sync($attachIds);
        }

        // 5. Create Promotions
        $promoWeekend = Promotion::updateOrCreate(
            ['code' => 'WEEKENDSERU20'],
            [
                'discount_percentage' => 20.00,
                'valid_until' => now()->addDays(30)->format('Y-m-d'),
            ]
        );

        $promoNewUser = Promotion::updateOrCreate(
            ['code' => 'PBO2SEWA50'],
            [
                'discount_percentage' => 50.00,
                'valid_until' => now()->addDays(60)->format('Y-m-d'),
            ]
        );

        // Many-to-Many with pivot: User redeems promotions
        $user->promotions()->syncWithoutDetaching([
            $promoWeekend->id => [
                'used_at' => now()->subDays(2),
                'discount_amount' => 70000.00,
            ],
            $promoNewUser->id => [
                'used_at' => now()->subDay(),
                'discount_amount' => 150000.00,
            ],
        ]);

        // 6. Create Completed Rental Transaction with details & return record
        $completedRental = Rental::firstOrCreate(
            ['rental_code' => 'RNT-202610-001'],
            [
                'user_id' => $user->id,
                'start_date' => now()->subDays(5)->format('Y-m-d'),
                'end_date' => now()->subDays(2)->format('Y-m-d'),
                'total_amount' => 1050000.00,
                'status' => 'completed',
                'notes' => 'Sewa mobil untuk perjalanan keluarga ke Loksado.',
            ]
        );

        RentalDetail::firstOrCreate(
            [
                'rental_id' => $completedRental->id,
                'rental_item_id' => $createdItems['toyota-all-new-avanza-2023']->id,
            ],
            [
                'quantity' => 1,
                'price_per_day' => 350000.00,
                'subtotal' => 1050000.00,
                'condition_before' => 'Sangat Mulus, bensin full',
            ]
        );

        // One-to-One: RentalReturn for completed rental
        RentalReturn::updateOrCreate(
            ['rental_id' => $completedRental->id],
            [
                'actual_return_date' => now()->subDays(2)->format('Y-m-d'),
                'late_days' => 0,
                'penalty_fee' => 0,
                'condition_after' => 'Mobil dikembalikan dalam keadaan bersih dan bensin terisi penuh.',
                'return_status' => 'verified',
            ]
        );

        // 7. Create Active Rental Transaction
        $activeRental = Rental::firstOrCreate(
            ['rental_code' => 'RNT-202610-002'],
            [
                'user_id' => $user->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(2)->format('Y-m-d'),
                'total_amount' => 420000.00,
                'status' => 'active',
                'notes' => 'Sewa perlengkapan camping dan motor trail akhir pekan.',
            ]
        );

        RentalDetail::firstOrCreate(
            [
                'rental_id' => $activeRental->id,
                'rental_item_id' => $createdItems['honda-crf-150l-trail-adventure']->id,
            ],
            [
                'quantity' => 1,
                'price_per_day' => 150000.00,
                'subtotal' => 300000.00,
                'condition_before' => 'Kondisi siap pakai offroad',
            ]
        );

        RentalDetail::firstOrCreate(
            [
                'rental_id' => $activeRental->id,
                'rental_item_id' => $createdItems['tenda-dome-eiger-guardian-4p']->id,
            ],
            [
                'quantity' => 1,
                'price_per_day' => 60000.00,
                'subtotal' => 120000.00,
                'condition_before' => 'Lengkap frame dan pasak',
            ]
        );

        // 8. Create Reviews (One-to-Many User -> Review, RentalItem -> Review)
        Review::firstOrCreate(
            [
                'user_id' => $user->id,
                'rental_item_id' => $createdItems['toyota-all-new-avanza-2023']->id,
            ],
            [
                'rating' => 5,
                'comment' => 'Mobil sangat nyaman dan irit untuk perjalanan jauh. Pelayanan rental sangat ramah dan profesional!',
            ]
        );

        Review::firstOrCreate(
            [
                'user_id' => $demoUser->id,
                'rental_item_id' => $createdItems['sony-alpha-a7-iv-mirrorless']->id,
            ],
            [
                'rating' => 5,
                'comment' => 'Kamera bekerja dengan sangat baik untuk job dokumentasi wedding, sensor dan lensa sangat bersih.',
            ]
        );
    }
}
