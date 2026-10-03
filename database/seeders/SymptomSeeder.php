<?php

namespace Database\Seeders;

use App\Models\Symptom;
use Illuminate\Database\Seeder;

class SymptomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $symptoms = [
            ['Kram perut', 'kram-perut', 'physical'],
            ['Sakit kepala', 'sakit-kepala', 'physical'],
            ['Nyeri payudara', 'nyeri-payudara', 'physical'],
            ['Nyeri punggung', 'nyeri-punggung', 'physical'],
            ['Lelah', 'lelah', 'physical'],
            ['Mual', 'mual', 'digestive'],
            ['Kembung', 'kembung', 'digestive'],
            ['Craving makanan', 'craving-makanan', 'digestive'],
            ['Jerawat', 'jerawat', 'skin'],
            ['Insomnia', 'insomnia', 'sleep'],
        ];

        foreach ($symptoms as [$name, $slug, $category]) {
            Symptom::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'category' => $category,
            ]);
        }
    }
}
