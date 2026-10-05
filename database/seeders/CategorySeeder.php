<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Processor', 'processor', 'cpu'],
            ['Motherboard', 'motherboard', 'motherboard'],
            ['Memory (RAM)', 'memory', 'memory-stick'],
            ['Graphics Card', 'graphics-card', 'gpu'],
            ['Storage', 'storage', 'hard-drive'],
            ['Power Supply', 'power-supply', 'zap'],
            ['Casing', 'casing', 'box'],
            ['CPU Cooler', 'cpu-cooler', 'fan'],
        ];

        foreach ($items as [$name, $slug, $icon]) {
            Category::updateOrCreate(['slug' => $slug], ['name' => $name, 'icon' => $icon]);
        }
    }
}