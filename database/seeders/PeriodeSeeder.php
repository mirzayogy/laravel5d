<?php

namespace Database\Seeders;

use App\Models\Periode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeriodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $data = collect([
            ['GANJIL 2025/2026', '251'],
            ['GENAP 2025/2026', '252'],
            ['GANJIL 2026/2027', '261'],
            ['GENAP 2026/2027', '262'],
        ])->map(fn($item) => [
            'periode' => $item[0],
            'singkatan' => $item[1],
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        Periode::insert($data);
    }
}
