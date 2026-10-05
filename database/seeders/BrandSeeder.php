<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = ['Intel', 'AMD', 'NVIDIA', 'ASUS', 'MSI', 'Gigabyte', 'Corsair', 'Kingston', 'Samsung', 'Cooler Master'];

        foreach ($brands as $name) {
            Brand::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}