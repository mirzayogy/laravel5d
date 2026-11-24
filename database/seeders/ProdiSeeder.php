<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Prodi::create(['kode_prodi' => 'TI', 'nama_prodi' => 'Teknik Informatika', 'nama_kaprodi' => 'Nama Kaprodi TI']);
        Prodi::create(['kode_prodi' => 'SI', 'nama_prodi' => 'Sistem Informasi', 'nama_kaprodi' => 'Nama Kaprodi SI']);
        Prodi::create(['kode_prodi' => 'MI', 'nama_prodi' => 'Manajemen Informatika', 'nama_kaprodi' => 'Nama Kaprodi MI']);
    }
}
