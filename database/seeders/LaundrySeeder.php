<?php

namespace Database\Seeders;

use App\Models\DetailTransaksi;
use App\Models\Karyawan;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;

class LaundrySeeder extends Seeder
{
    public function run(): void
    {
        $budi = Pelanggan::create([
            'nama'   => 'Budi Santoso',
            'no_hp'  => '081234567890',
            'alamat' => 'Jl. A. Yani KM 36, Banjarbaru',
        ]);

        Pelanggan::create([
            'nama'   => 'Siti Aminah',
            'no_hp'  => '085212345678',
            'alamat' => 'Jl. Trikora, Banjarbaru',
        ]);

        // Password di-hash otomatis lewat cast di model Karyawan.
        // Ganti 'password123' sebelum dipakai sungguhan.
        Karyawan::create([
            'nama'     => 'Admin Laundry',
            'username' => 'admin',
            'password' => 'password123',
            'role'     => 'admin',
        ]);

        $kasir = Karyawan::create([
            'nama'     => 'Kasir 1',
            'username' => 'kasir1',
            'password' => 'password123',
            'role'     => 'kasir',
        ]);

        Layanan::create(['nama_layanan' => 'Cuci Kering',  'harga_per_kg' => 5000, 'estimasi_hari' => 2]);
        $cuciSetrika = Layanan::create(['nama_layanan' => 'Cuci Setrika', 'harga_per_kg' => 7000, 'estimasi_hari' => 3]);
        Layanan::create(['nama_layanan' => 'Setrika Saja', 'harga_per_kg' => 4000, 'estimasi_hari' => 1]);

        $transaksi = Transaksi::create([
            'id_pelanggan' => $budi->id_pelanggan,
            'id_karyawan'  => $kasir->id_karyawan,
            'tgl_masuk'    => '2026-10-05',
            'tgl_selesai'  => '2026-10-08',
            'status'       => 'dicuci',
            'total_harga'  => 0,
        ]);

        $berat = 5;
        DetailTransaksi::create([
            'id_transaksi' => $transaksi->id_transaksi,
            'id_layanan'   => $cuciSetrika->id_layanan,
            'berat_kg'     => $berat,
            'subtotal'     => $berat * $cuciSetrika->harga_per_kg,
        ]);

        $transaksi->hitungTotal();

        Pembayaran::create([
            'id_transaksi' => $transaksi->id_transaksi,
            'tgl_bayar'    => '2026-10-05',
            'metode'       => 'tunai',
            'jumlah_bayar' => 20000,
        ]);
    }
}
