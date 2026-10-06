<?php

namespace Database\Seeders;

use App\Models\Mechanic;
use Illuminate\Database\Seeder;

class MechanicSeeder extends Seeder
{
    public function run(): void
    {
        $mechanics = [
            [
                'name' => 'Bambang Sutrisno',
                'phone' => '081234567891',
                'specialization' => 'Mesin & Transmisi',
                'experience_years' => 10,
                'status' => 'Available',
            ],
            [
                'name' => 'Doni Prasetyo',
                'phone' => '081234567892',
                'specialization' => 'Kelistrikan & AC',
                'experience_years' => 7,
                'status' => 'Available',
            ],
            [
                'name' => 'Eko Saputra',
                'phone' => '081234567893',
                'specialization' => 'Kaki-kaki & Spooring',
                'experience_years' => 5,
                'status' => 'Available',
            ],
            [
                'name' => 'Rian Hidayat',
                'phone' => '081234567894',
                'specialization' => 'Perawatan Berkala & Quick Service',
                'experience_years' => 4,
                'status' => 'Busy',
            ],
        ];

        foreach ($mechanics as $mechanic) {
            Mechanic::create($mechanic);
        }
    }
}
