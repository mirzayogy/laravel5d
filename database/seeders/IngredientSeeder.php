<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            'Nasi putih' => 'piring',
            'Telur' => 'butir',
            'Bawang merah' => 'siung',
            'Bawang putih' => 'siung',
            'Cabai' => 'buah',
            'Kecap manis' => 'sdm',
            'Garam' => 'sdt',
            'Gula pasir' => 'sdm',
            'Minyak goreng' => 'sdm',
            'Ayam' => 'gram',
            'Kangkung' => 'ikat',
            'Kunyit' => 'ruas',
            'Jahe' => 'ruas',
            'Pisang' => 'buah',
            'Tepung terigu' => 'gram',
            'Teh celup' => 'kantong',
            'Air' => 'ml',
        ];

        foreach ($ingredients as $name => $unit) {
            Ingredient::updateOrCreate(['name' => $name], ['unit' => $unit]);
        }
    }
}
