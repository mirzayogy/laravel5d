# P01: Database Design and Table Relationships

| | |
|---|---|
| **Student** | Wira Yudha Pamuji |
| **NPM** | 2410010396 |
| **Class** | TI 5D REG BJB |
| **Status** | ✅ Done |
| **Started** | 2026-10-06 |
| **Completed** | 2026-10-06 |
| **Branch** | `feature/database-relations` |
| **Fork Repository** | [wirayudhapamuji123/laravel5d](https://github.com/wirayudhapamuji123/laravel5d) |
| **Upstream Repository** | [mirzayogy/laravel5d](https://github.com/mirzayogy/laravel5d) |

## Goal

Merancang dan mengimplementasikan basis data sistem rental **RentHub** (Equipment & Vehicle Rental System) dengan fokus pada penguasaan seluruh relasi tabel pada Laravel Eloquent:
- **One-to-One**: `User` ↔ `Profile`, `RentalItem` ↔ `ItemSpecification`, `Rental` ↔ `RentalReturn`
- **One-to-Many**: `Category` → `RentalItem`, `User` → `Rental`, `User` → `Review`, `RentalItem` → `Review`, `Rental` → `RentalDetail`, `RentalItem` → `RentalDetail`
- **Many-to-Many**: `RentalItem` ↔ `Tag`
- **Many-to-Many dengan data pivot**: `Rental` ↔ `RentalItem` (pivot `quantity`, `price_per_day`, `subtotal`, `condition_before`), `User` ↔ `Promotion` (pivot `used_at`, `discount_amount`)
- **Has-Many-Through**: `User` → `RentalDetail` melalui `Rental`, `Category` → `RentalDetail` melalui `RentalItem`, `Category` → `Review` melalui `RentalItem`

---

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| **J1** | Database design and ERD | ✅ Done | 2026-10-06 | [`docs/database/erd.md`](../database/erd.md) · [commit 3a79068](https://github.com/wirayudhapamuji123/laravel5d/commit/3a79068) |
| **J2** | Migrations: 12 entity and pivot tables | ✅ Done | 2026-10-06 | [`database/migrations`](../../database/migrations) · [commit 9e0b0c3](https://github.com/wirayudhapamuji123/laravel5d/commit/9e0b0c3) |
| **J3** | Eloquent models and relationships | ✅ Done | 2026-10-06 | [`app/Models`](../../app/Models) · [commit 1a124b0](https://github.com/wirayudhapamuji123/laravel5d/commit/1a124b0) |
| **J4** | Factories and Seeders | ✅ Done | 2026-10-06 | [`database/factories`](../../database/factories), [`database/seeders`](../../database/seeders) · [commit 6ff5304](https://github.com/wirayudhapamuji123/laravel5d/commit/6ff5304) |
| **J5** | Automated relationship test suite | ✅ Done | 2026-10-06 | [`tests/Feature/DatabaseRelationsTest.php`](../../tests/Feature/DatabaseRelationsTest.php) · [commit 15858be](https://github.com/wirayudhapamuji123/laravel5d/commit/15858be) |
| **J6** | README and Fork guide | ✅ Done | 2026-10-06 | [`README.md`](../../README.md), [`docs/guides/fork-guide.md`](../guides/fork-guide.md) · [commit 4bcb58d](https://github.com/wirayudhapamuji123/laravel5d/commit/4bcb58d) |

---

### J1: Database design and ERD
- **Status:** ✅ Done (2026-10-06)
- **What:** Diagram ERD Mermaid lengkap 12 tabel dengan kardinalitas detail dan ringkasan metode Eloquent.
- **Proof:** [`docs/database/erd.md`](../database/erd.md)

### J2: Migrations
- **Status:** ✅ Done (2026-10-06)
- **What:** Pembuatan migrasi tabel `profiles`, `categories`, `rental_items`, `item_specifications`, `tags`, `item_tag`, `rentals`, `rental_details`, `rental_returns`, `reviews`, `promotions`, dan `promotion_user`.
- **Proof:** Folder [`database/migrations`](../../database/migrations)
- **Verified:** `php artisan migrate:fresh` berhasil tanpa error.

### J3: Models and relationships
- **Status:** ✅ Done (2026-10-06)
- **What:** Implementasi model Eloquent dengan casts dan fungsi relasi: `hasOne`, `belongsTo`, `hasMany`, `belongsToMany` (+ `withPivot`, `withTimestamps`), dan `hasManyThrough`.
- **Proof:** Folder [`app/Models`](../../app/Models)

### J4: Factories and Seeders
- **Status:** ✅ Done (2026-10-06)
- **What:** Pembuatan 10 factory model dan 2 seeder (`CategorySeeder`, `RentHubSeeder`) dengan data realistis.
- **Proof:** Folder [`database/factories`](../../database/factories) dan [`database/seeders`](../../database/seeders)
- **Verified:** `php artisan migrate:fresh --seed` berhasil dijalankan.

### J5: Automated relationship tests
- **Status:** ✅ Done (2026-10-06)
- **What:** Test suite komprehensif menguji seluruh jenis relasi dengan assertion lengkap.
- **Proof:** [`tests/Feature/DatabaseRelationsTest.php`](../../tests/Feature/DatabaseRelationsTest.php)
- **Verified:** `php artisan test` menghasilkan 15 passed tests, 51 assertions, 0 failure.

### J6: README and Fork guide
- **Status:** ✅ Done (2026-10-06)
- **What:** Dokumentasi identitas mahasiswa, deskripsi aplikasi, cara instalasi, serta panduan alur fork dan Pull Request ke repository dosen.
- **Proof:** [`README.md`](../../README.md) dan [`docs/guides/fork-guide.md`](../guides/fork-guide.md)

---

## Next Steps (Fase Berikutnya)

- **P02**: Autentikasi dan Otorisasi (Multi-role: Administrator & Customer)
- **P03**: Manajemen Katalog & Transaksi Sewa (CRUD Item, Booking Rental, Verifikasi Pengembalian)
- **P04**: Dashboard Interaktif dan Laporan Pendapatan Rental
