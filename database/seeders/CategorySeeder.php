<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the built-in fish sales categories.
     */
    public function run(): void
    {
        $categories = [
            ['Ikan Hias', 'ikan-hias', '🐠', '#3b82f6'],
            ['Ikan Konsumsi', 'ikan-konsumsi', '🐟', '#10b981'],
            ['Ikan Laut', 'ikan-laut', '🌊', '#06b6d4'],
            ['Ikan Tawar', 'ikan-tawar', '💧', '#6366f1'],
            ['Bibit & Benih Ikan', 'bibit-benih-ikan', '🌱', '#8b5cf6'],
            ['Pakan Ikan', 'pakan-ikan', '📦', '#f59e0b'],
            ['Perlengkapan Akuarium', 'perlengkapan-akuarium', '🫧', '#ec4899'],
            ['Obat & Nutrisi Ikan', 'obat-nutrisi-ikan', '💊', '#ef4444'],
        ];

        foreach ($categories as [$name, $slug, $icon, $color]) {
            Category::updateOrCreate(['slug' => $slug], compact('name', 'icon', 'color'));
        }
    }
}
