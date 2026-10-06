# P01: Database Design and Table Relationships - ServiceHub

| | |
|---|---|
| **Student** | Muhammad Khairil Ilham |
| **NPM** | 2410010205 |
| **Class** | TI 5D REG BJB |
| **Phase** | P01: Database Design & Relationships |
| **Status** | ✅ Done (2026-10-07) |
| **Branch** | `feature/database-relations` |
| **Pull Request** | <https://github.com/mirzayogy/laravel5d/pull/33> |
| **Repository** | [muhammadiril56/laravel5d](https://github.com/muhammadiril56/laravel5d) |

## Goal
Merancang dan mengimplementasikan skema database relasional untuk **ServiceHub** (Sistem Manajemen Bengkel, Booking Servis Kendaraan, Suku Cadang, dan Mekanik) menggunakan Laravel Eloquent ORM dengan mencakup seluruh jenis relasi:
- **One-to-One (1:1)**: `User <-> UserProfile`, `ServiceBooking <-> InspectionReport`, `ServiceBooking <-> Invoice`
- **One-to-Many (1:N)**: `User -> Vehicle`, `Vehicle -> ServiceBooking`, `ServiceCategory -> ServicePackage`, `Mechanic -> ServiceBooking`, `ServiceBooking -> ServiceReview`
- **Many-to-Many (N:M with Pivot)**: `ServiceBooking <-> ServicePackage`, `ServiceBooking <-> SparePart`, `User <-> Promotion`
- **Has-Many-Through**: `User -> ServiceBooking` (through `Vehicle`)

---

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | ERD Design with Mermaid | ✅ Done | 2026-10-07 | [`docs/database/erd.md`](../database/erd.md) |
| J2 | Database Migrations (14 Tables & Pivots) | ✅ Done | 2026-10-07 | [`database/migrations`](../../database/migrations) |
| J3 | Eloquent Models & Relationships | ✅ Done | 2026-10-07 | [`app/Models`](../../app/Models) |
| J4 | Factories & Relational Seeders | ✅ Done | 2026-10-07 | [`database/factories`](../../database/factories), [`database/seeders`](../../database/seeders) |
| J5 | Automated Feature Testing (12 Tests, 47 Assertions) | ✅ Done | 2026-10-07 | [`tests/Feature/DatabaseRelationsTest.php`](../../tests/Feature/DatabaseRelationsTest.php) |

---

### J1: ERD Design with Mermaid
- **Status:** ✅ Done, 2026-10-07
- **What:** Menyusun Entity Relationship Diagram (ERD) lengkap dengan 14 entitas/tabel beserta relasi 1:1, 1:N, N:M (pivot), dan Has-Many-Through.
- **Proof:** [`docs/database/erd.md`](../database/erd.md)

### J2: Database Migrations
- **Status:** ✅ Done, 2026-10-07
- **What:** Membuat 14 file migrasi dengan foreign key constraints, cascade rules, indexing, dan unique constraints:
  - `user_profiles` (1:1 dengan `users`)
  - `vehicles` (1:N dengan `users`)
  - `mechanics`
  - `service_categories`
  - `service_packages` (1:N dengan `service_categories`)
  - `spare_parts`
  - `promotions`
  - `service_bookings` (1:N dengan `vehicles` & `mechanics`)
  - `booking_service_package` (pivot N:M)
  - `booking_spare_part` (pivot N:M)
  - `promotion_user` (pivot N:M)
  - `inspection_reports` (1:1 dengan `service_bookings`)
  - `invoices` (1:1 dengan `service_bookings`)
  - `service_reviews` (1:N dengan `service_bookings`)
- **Verified:** `php artisan migrate:fresh` berhasil dieksekusi tanpa error.

### J3: Eloquent Models & Relationships
- **Status:** ✅ Done, 2026-10-07
- **What:** Membuat dan mengonfigurasi model Eloquent beserta metode relasi, `$fillable`, dan `$casts`:
  - `User`: `hasOne(UserProfile)`, `hasMany(Vehicle)`, `hasManyThrough(ServiceBooking, Vehicle)`, `belongsToMany(Promotion)`
  - `UserProfile`: `belongsTo(User)`
  - `Vehicle`: `belongsTo(User)`, `hasMany(ServiceBooking)`
  - `Mechanic`: `hasMany(ServiceBooking)`
  - `ServiceCategory`: `hasMany(ServicePackage)`
  - `ServicePackage`: `belongsTo(ServiceCategory)`, `belongsToMany(ServiceBooking)`
  - `SparePart`: `belongsToMany(ServiceBooking)`
  - `Promotion`: `belongsToMany(User)`
  - `ServiceBooking`: `belongsTo(Vehicle)`, `belongsTo(Mechanic)`, `hasOne(InspectionReport)`, `hasOne(Invoice)`, `hasMany(ServiceReview)`, `belongsToMany(ServicePackage)`, `belongsToMany(SparePart)`
  - `InspectionReport`: `belongsTo(ServiceBooking)`
  - `Invoice`: `belongsTo(ServiceBooking)`
  - `ServiceReview`: `belongsTo(ServiceBooking)`

### J4: Factories & Relational Seeders
- **Status:** ✅ Done, 2026-10-07
- **What:** Membuat factory untuk setiap model dan seeder komprehensif (`ServiceCategorySeeder`, `SparePartSeeder`, `MechanicSeeder`, `PromotionSeeder`, `DatabaseSeeder`).
- **Verified:** Menjalankan `php artisan migrate:fresh --seed` sukses mengisi data demo realistis.

### J5: Automated Feature Testing
- **Status:** ✅ Done, 2026-10-07
- **What:** Mengimplementasikan test suite di `tests/Feature/DatabaseRelationsTest.php` untuk memvalidasi integritas seluruh jenis relasi.
- **Verified:** `php artisan test --filter=DatabaseRelationsTest` menghasilkan **12 tests, 47 assertions, 100% Passed**.

---

## How to Verify
```bash
# 1. Pastikan dependensi terpasang
composer install

# 2. Salin .env dan generate app key (bila belum)
cp .env.example .env
php artisan key:generate

# 3. Jalankan migrasi dan seeder
php artisan migrate:fresh --seed

# 4. Jalankan pengujian relasi otomatis
php artisan test --filter=DatabaseRelationsTest
```
Semua 12 pengujian relasi tabel dipastikan lulus (**100% Passed**).
