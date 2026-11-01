# Progress Report — P01 Database Design

## Assignment 1: Table Relationships — PenyewaanCamp

| | |
|---|---|
| **Student** | Maulida Hasanah |
| **NPM** | 2410010242 |
| **Class** | Teknik Informatika 5D RegP BJB |
| **Phase** | P01: Database Design |
| **Status** | Done (1 November 2026) |
| **Fork / branch** | https://github.com/maulidahasanah14/laravel5d / `feature/p01-database-design` |

## 1. Tujuan
Merancang struktur database sistem **PenyewaanCamp** (penyewaan perlengkapan camping) beserta relasi antar tabelnya, lalu mengimplementasikannya dalam migration dan model Eloquent Laravel.

## 2. Entitas
| Tabel | Fungsi |
|---|---|
| `users` | Data pengguna/penyewa (bawaan Laravel) |
| `kategoris` | Kategori barang (tenda, carrier, alat masak, dll.) |
| `barangs` | Daftar perlengkapan camping yang bisa disewa |
| `penyewaans` | Transaksi sewa (header) |
| `detail_penyewaans` | Rincian barang per transaksi sewa |
| `pembayarans` | Pembayaran untuk satu transaksi sewa |

## 3. Relasi Antar Tabel
| Tabel A | Relasi | Tabel B | Foreign key | Aturan hapus |
|---|---|---|---|---|
| `kategoris` | 1 : N | `barangs` | `barangs.kategori_id` | restrict |
| `users` | 1 : N | `penyewaans` | `penyewaans.user_id` | restrict |
| `penyewaans` | 1 : N | `detail_penyewaans` | `detail_penyewaans.penyewaan_id` | cascade |
| `barangs` | 1 : N | `detail_penyewaans` | `detail_penyewaans.barang_id` | restrict |
| `penyewaans` | N : N | `barangs` | lewat tabel pivot `detail_penyewaans` | — |
| `penyewaans` | 1 : 1 | `pembayarans` | `pembayarans.penyewaan_id` (unique) | cascade |

ERD lengkap: [docs/erd.md](../erd.md)

## 4. Pekerjaan yang Dilakukan
| Job | Deskripsi | Status |
|---|---|---|
| J1 | Analisis kebutuhan dan penentuan entitas sistem PenyewaanCamp | Done |
| J2 | Pembuatan ERD (Mermaid) di `docs/erd.md` | Done |
| J3 | Pembuatan 5 migration beserta foreign key dan constraint | Done |
| J4 | Pembuatan 5 model Eloquent dan relasi pada model `User` | Done |
| J5 | Penulisan progress report dan pengujian relasi lewat tinker | Done |

## 5. Keputusan Desain
- **`detail_penyewaans` sebagai pivot**: relasi many-to-many antara penyewaan dan barang butuh data tambahan (`jumlah`, `harga_satuan`, `subtotal`), jadi dibuat tabel tersendiri.
- **`harga_satuan` disimpan di detail**: harga sewa barang bisa berubah, tetapi transaksi lama harus tetap memakai harga saat disewa.
- **Unique `(penyewaan_id, barang_id)`**: satu barang tidak muncul dua kali dalam satu transaksi.
- **`pembayarans.penyewaan_id` unique**: memastikan relasi satu penyewaan hanya punya satu pembayaran.
- **`restrictOnDelete` pada user, kategori, dan barang**: data master tidak boleh terhapus jika sudah dipakai transaksi.

## 6. Cara Menjalankan dan Verifikasi
```bash
composer install
cp .env.example .env
php artisan key:generate
# atur DB_DATABASE, DB_USERNAME, DB_PASSWORD di .env
php artisan migrate:fresh
php artisan tinker
```

Di dalam tinker:
```php
$u = App\Models\User::factory()->create();
$k = App\Models\Kategori::create(['nama_kategori' => 'Tenda']);
$b = App\Models\Barang::create(['kategori_id' => $k->id, 'nama_barang' => 'Tenda Dome 4P', 'harga_sewa_per_hari' => 50000, 'stok' => 5]);
$p = App\Models\Penyewaan::create(['user_id' => $u->id, 'tanggal_sewa' => '2026-11-10', 'tanggal_kembali' => '2026-11-12', 'total_harga' => 100000]);
$p->barangs()->attach($b->id, ['jumlah' => 1, 'harga_satuan' => 50000, 'subtotal' => 100000]);
$p->pembayaran()->create(['tanggal_bayar' => '2026-11-10', 'jumlah_bayar' => 100000, 'metode' => 'transfer', 'status' => 'lunas']);

$p->load('user', 'barangs.kategori', 'pembayaran');
$k->barangs;            // relasi 1:N
$b->penyewaans;         // relasi N:N
$p->pembayaran;         // relasi 1:1
```

Hasil yang diharapkan: semua migration sukses, semua relasi mengembalikan data tanpa error.

### Bukti hasil (isi dengan screenshot milik sendiri)
- [ ] Screenshot output `php artisan migrate:fresh`
- [ ] Screenshot struktur tabel di database (phpMyAdmin/DBeaver/TablePlus)
- [ ] Screenshot output tinker pemanggilan relasi

## 7. Belum Dikerjakan
- Seeder dan factory data dummy untuk tabel selain `users`
- Controller, route, dan tampilan (UI)
- Autentikasi, otorisasi, dan validasi input
- Logika bisnis (pengurangan stok, perhitungan denda keterlambatan)
