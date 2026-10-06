<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\ServicePackage;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Perawatan Berkala',
                'slug' => 'perawatan-berkala',
                'description' => 'Servis rutin sesuai kelipatan kilometer untuk menjaga performa optimal kendaraan.',
                'packages' => [
                    [
                        'name' => 'Servis Berkala 10.000 KM',
                        'description' => 'Ganti oli mesin, cek 21 titik komponen, rotasi ban, dan pembersihan rem.',
                        'base_price' => 350000,
                        'estimated_duration_minutes' => 60,
                    ],
                    [
                        'name' => 'Servis Berkala 40.000 KM (Major Service)',
                        'description' => 'Servis besar mencakup kuras radiator, ganti busi, filter oli, filter udara, dan tune up.',
                        'base_price' => 750000,
                        'estimated_duration_minutes' => 120,
                    ],
                ],
            ],
            [
                'name' => 'Mesin & Performa (Tune-Up)',
                'slug' => 'mesin-performa',
                'description' => 'Optimalisasi sistem pembakaran, injektor, dan ruang bakar.',
                'packages' => [
                    [
                        'name' => 'Tune Up Injeksi & Carbon Clean',
                        'description' => 'Pembersihan throttle body, gurah mesin ruang bakar, dan scanner diagnosis ECU.',
                        'base_price' => 450000,
                        'estimated_duration_minutes' => 90,
                    ],
                    [
                        'name' => 'Overhaul & Kalibrasi Klep',
                        'description' => 'Pemeriksaan kompresi, penyetelan celah katup, dan pembersihan intake.',
                        'base_price' => 850000,
                        'estimated_duration_minutes' => 180,
                    ],
                ],
            ],
            [
                'name' => 'AC & Kelistrikan',
                'slug' => 'ac-kelistrikan',
                'description' => 'Pengecekan dan perawatan sistem pendingin kabin serta kelistrikan kendaraan.',
                'packages' => [
                    [
                        'name' => 'Servis AC Ringan & Cuci Evaporator',
                        'description' => 'Fogging anti bakteri, ganti filter kabin, dan cuci blower evaporator.',
                        'base_price' => 275000,
                        'estimated_duration_minutes' => 45,
                    ],
                    [
                        'name' => 'Flushing Oli Kompresor & Isi Freon R134a',
                        'description' => 'Kuras oli kompresor dengan mesin otomatis dan pengisian freon murni baru.',
                        'base_price' => 550000,
                        'estimated_duration_minutes' => 90,
                    ],
                ],
            ],
            [
                'name' => 'Kaki-Kaki, Rem & Spooring',
                'slug' => 'kaki-kaki-rem-spooring',
                'description' => 'Perawatan sistem pengereman, suspensi, dan kestabilan roda kemudi.',
                'packages' => [
                    [
                        'name' => 'Spooring 3D & Balancing 4 Roda',
                        'description' => 'Penyelarasan sudut camber, caster, toe dengan sensor digital 3D.',
                        'base_price' => 200000,
                        'estimated_duration_minutes' => 45,
                    ],
                    [
                        'name' => 'Servis Rem 4 Roda (Brake Overhaul)',
                        'description' => 'Bongkar, amplas, pembersihan kaliper rem, bubut disc brake, dan ganti minyak rem.',
                        'base_price' => 300000,
                        'estimated_duration_minutes' => 60,
                    ],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $packages = $catData['packages'];
            unset($catData['packages']);

            $category = ServiceCategory::create($catData);

            foreach ($packages as $pkg) {
                $category->servicePackages()->create($pkg);
            }
        }
    }
}
