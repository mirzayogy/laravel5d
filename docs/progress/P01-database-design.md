# Progress Report: P01 Database Design

| | |
|---|---|
| **Student** | Luchyana Desi Safitri |
| **NPM** | 2410010230 |
| **Class** | TI 5D REG BJB |
| **Assignment** | Assignment 1: Table Relationships: Kost Area |
| **Phase** | P01: Database Design |
| **Fork / branch** | [Luchyana-Desi/laravel5d](https://github.com/Luchyana-Desi/laravel5d) / `feature/p01-database-design` |
| **Pull request** | [#53](https://github.com/mirzayogy/laravel5d/pull/53) |
| **Started** | 1 November 2026 |
| **Completed** | 5 November 2026 |
| **Status** | Done |

## Ringkasan

Fase P01 mengerjakan desain database untuk proyek **Kost Area**, yaitu platform manajemen dan booking kos dengan dua kost: **Kost Area Putra** dan **Kost Area Putri**. Pekerjaan mencakup perancangan ERD lengkap, migration, model Eloquent beserta relasinya, factory, seeder, controller, route, dan halaman web untuk menampilkan data.

## Status job

| Job | Description | Status | Completed |
|---|---|---|---|
| J1 | README project dan aturan bisnis Kost Area | Done | 5 Nov 2026 |
| J2 | Migration 7 tabel (users, kosts, rooms, facilities, facility_room, tenants, bookings, payments) dengan foreign key, unique constraint, dan kolom enum | Done | 5 Nov 2026 |
| J3 | Model Eloquent beserta relasi (`hasMany`, `belongsTo`, `belongsToMany`) dan relasi `kosts()` di `User` | Done | 5 Nov 2026 |
| J4 | ERD lengkap (`docs/erd.md`) dan progress report | Done | 5 Nov 2026 |
| J5 | Factory untuk 6 model dan `KostAreaSeeder` (2 kost, 10 kamar, 6 penyewa, 6 booking, 20 pembayaran) | Done | 5 Nov 2026 |
| J6 | Controller dan route (`index` dan `show`) untuk 6 tabel, ditambah Dashboard | Done | 5 Nov 2026 |
| J7 | Tampilan Blade: layout, Dashboard, dan halaman daftar serta detail untuk 6 tabel | Done | 5 Nov 2026 |

## Detail J1 sampai J4

**Keputusan desain:**
- Kost khusus putra atau putri (`kosts.type`), tidak ada kost campur. Kamar mengikuti jenis kost-nya.
- Penyewa memiliki jenis kelamin (`tenants.gender`) agar dapat dicocokkan dengan jenis kost.
- Booking menyimpan tarif saat dipesan (`price_per_unit`), jenis sewa harian atau bulanan, deposit, dan total.
- Pembayaran memiliki tipe (deposit, sewa, denda, pengembalian), jatuh tempo, bukti bayar, dan verifikasi oleh pemilik.

**Relasi antar tabel** (diagram lengkap di [docs/erd.md](../erd.md)):

| Relasi | Jenis | Implementasi |
|---|---|---|
| User -> Kost | one-to-many | `kosts.user_id` |
| Kost -> Room | one-to-many | `rooms.kost_id` |
| Room <-> Facility | many-to-many | pivot `facility_room` |
| Room -> Booking | one-to-many | `bookings.room_id` |
| Tenant -> Booking | one-to-many | `bookings.tenant_id` |
| Booking -> Booking | self-reference | `bookings.previous_booking_id` |
| Booking -> Payment | one-to-many | `payments.booking_id` |
| User -> Payment | one-to-many | `payments.verified_by` |

**ERD lengkap vs yang sudah dibuat:** `docs/erd.md` memuat rancangan database lengkap, termasuk tabel yang masih rencana (`facility_kost`, `images`, `maintenance_requests`) dan kolom tambahan (`users.role`, `facilities.scope`, profil penyewa). Tabel status implementasi ada di dokumen itu.

## Detail J5

- `database/factories/`: KostFactory, RoomFactory, FacilityFactory, TenantFactory, BookingFactory, PaymentFactory
- `database/seeders/KostAreaSeeder.php`, dipanggil dari `DatabaseSeeder.php`

**Data yang dihasilkan seeder:**

| Tabel | Jumlah | Keterangan |
|---|---|---|
| users | 1 | Pemilik kost |
| facilities | 5 | AC, WiFi, Kamar Mandi Dalam, Kasur, Lemari |
| kosts | 2 | Kost Area Putra dan Kost Area Putri |
| rooms | 10 | 5 per kost (3 terisi, 1 tersedia, 1 perbaikan) |
| tenants | 6 | Jenis kelamin sesuai jenis kost |
| bookings | 6 | 4 sewa bulanan, 2 sewa harian |
| payments | 20 | Status lunas, menunggu verifikasi, terlambat, dan belum dibayar |

## Detail J6 dan J7

**Controller dan route:** `DashboardController` serta controller untuk Kost, Room, Facility, Tenant, Booking, dan Payment. Setiap tabel memiliki route `index` dan `show` (`Route::resource(...)->only(['index', 'show'])`), serta Dashboard di `/`. Data dimuat dengan eager loading.

**Halaman web:**

| Alamat | Isi |
|---|---|
| `/` | Dashboard ringkasan |
| `/kosts`, `/kosts/{id}` | Daftar dan detail kost |
| `/rooms`, `/rooms/{id}` | Daftar dan detail kamar |
| `/facilities`, `/facilities/{id}` | Daftar dan detail fasilitas |
| `/tenants`, `/tenants/{id}` | Daftar dan detail penyewa |
| `/bookings`, `/bookings/{id}` | Daftar dan detail booking |
| `/payments`, `/payments/{id}` | Daftar dan detail pembayaran |

Tampilan hanya menampilkan data. Fitur tambah, ubah, dan hapus (CRUD) dikerjakan di Assignment 2.

## Cara verifikasi

```bash
php artisan migrate:fresh --seed
php artisan serve
```

Semua migration berstatus `DONE`, lalu `KostAreaSeeder` berstatus `DONE` tanpa error. Buka <http://localhost:8000> untuk melihat Dashboard dan menu lainnya.

## Kendala dan solusi

| Kendala | Solusi |
|---|---|
| Saat menyalin file, satu folder di-drag dan memilih Replace sehingga isi folder bawaan project (`app`, `database`, `docs`) tertimpa dan terhapus | Dipulihkan dengan `git restore .`, lalu file baru disalin satu per satu |
| `use HasMany` di `User.php` salah posisi (sebelum `namespace`) sehingga muncul error | Dipindahkan ke bawah `namespace` bersama import lainnya |
| `package-lock.json` hasil `npm install` ikut terdeteksi sebagai perubahan | Tidak di-commit karena bukan bagian tugas |
| README awal menjelaskan tabel dan kolom yang belum ada di migration | README disesuaikan dengan migration yang dijalankan, sedangkan rancangan lengkap dipindahkan ke `docs/erd.md` dengan status implementasi |
| Kost awalnya memiliki tipe `campur` | Diubah menjadi `putra` atau `putri` di tabel kost, dan jenis kelamin penyewa ditambahkan |

## Belum dikerjakan

- Fasilitas kost (`facility_kost`) dan foto (`images`)
- Laporan kerusakan (`maintenance_requests`)
- Profil penghuni, autentikasi, dan akses berbasis peran
- Pemeriksaan otomatis jenis kelamin saat booking, serta proses otomatis (pembatalan booking, penagihan, penandaan pembayaran terlambat)
- CRUD dengan template AdminLTE (Assignment 2)

## Bukti

- README: [README.md](https://github.com/Luchyana-Desi/laravel5d/blob/feature/p01-database-design/README.md)
- ERD lengkap: [docs/erd.md](https://github.com/Luchyana-Desi/laravel5d/blob/feature/p01-database-design/docs/erd.md)
- Migrations: [database/migrations](https://github.com/Luchyana-Desi/laravel5d/tree/feature/p01-database-design/database/migrations)
- Models: [app/Models](https://github.com/Luchyana-Desi/laravel5d/tree/feature/p01-database-design/app/Models)
- Factories: [database/factories](https://github.com/Luchyana-Desi/laravel5d/tree/feature/p01-database-design/database/factories)
- Seeders: [database/seeders](https://github.com/Luchyana-Desi/laravel5d/tree/feature/p01-database-design/database/seeders)
- Controllers: [app/Http/Controllers](https://github.com/Luchyana-Desi/laravel5d/tree/feature/p01-database-design/app/Http/Controllers)
- Routes: [routes/web.php](https://github.com/Luchyana-Desi/laravel5d/blob/feature/p01-database-design/routes/web.php)
- Views: [resources/views](https://github.com/Luchyana-Desi/laravel5d/tree/feature/p01-database-design/resources/views)
- Riwayat commit branch: [commits/feature/p01-database-design](https://github.com/Luchyana-Desi/laravel5d/commits/feature/p01-database-design)