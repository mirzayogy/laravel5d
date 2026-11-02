<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['Basic Grooming', 75000, 60],
            ['Full Grooming', 150000, 120],
            ['Bath and Dry', 60000, 45],
            ['Haircut', 80000, 60],
            ['Nail Trimming', 30000, 20],
            ['Ear Cleaning', 35000, 20],
            ['Flea Treatment', 100000, 90],
        ];

        foreach ($services as [$name, $price, $duration]) {
            Service::updateOrCreate(
                ['name' => $name],
                [
                    'description' => 'Layanan ' . $name
                        . ' untuk perawatan hewan peliharaan.',
                    'price' => $price,
                    'duration_minutes' => $duration,
                    'is_active' => true,
                ]
            );
        }
    }
}
