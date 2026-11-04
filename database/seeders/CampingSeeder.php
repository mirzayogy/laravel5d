<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Penyewaan;
use App\Models\User;
use Illuminate\Database\Seeder;

class CampingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        $tenda   = Kategori::create(['nama_kategori' => 'Tenda', 'deskripsi' => 'Tenda berbagai kapasitas']);
        $carrier = Kategori::create(['nama_kategori' => 'Carrier', 'deskripsi' => 'Tas gunung']);

        $dome = Barang::create([
            'kategori_id' => $tenda->id,
            'nama_barang' => 'Tenda Dome 4P',
            'harga_sewa_per_hari' => 50000,
            'stok' => 5,
        ]);
        $c60 = Barang::create([
            'kategori_id' => $carrier->id,
            'nama_barang' => 'Carrier 60L',
            'harga_sewa_per_hari' => 35000,
            'stok' => 8,
        ]);

        $sewa = Penyewaan::create([
            'user_id' => $user->id,
            'tanggal_sewa' => '2026-11-10',
            'tanggal_kembali' => '2026-11-12',
            'total_harga' => 170000,
            'status' => 'disewa',
        ]);

        $sewa->barangs()->attach([
            $dome->id => ['jumlah' => 1, 'harga_satuan' => 50000, 'subtotal' => 100000],
            $c60->id  => ['jumlah' => 1, 'harga_satuan' => 35000, 'subtotal' => 70000],
        ]);

        $sewa->pembayaran()->create([
            'tanggal_bayar' => '2026-11-10',
            'jumlah_bayar' => 170000,
            'metode' => 'transfer',
            'status' => 'lunas',
        ]);
    }
}