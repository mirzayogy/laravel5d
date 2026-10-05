# PartsHub – Database Design (Laravel)

**Assignment 1 – Table Relationships**
Ahmad Zainal Febryan – 2410010414 – TI 5C REG BJB

PartsHub adalah marketplace jual beli komponen PC. Selain produk, pesanan, dan ulasan, database-nya memodelkan **spesifikasi teknis** dan **kecocokan antar komponen** (misalnya socket CPU vs socket motherboard).

## Tech Stack

- Laravel 13, PHP 8.4
- SQLite

## Cara Menjalankan

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
```

> Jika `composer install` menolak versi PHP (mis. PHP 8.4.0 vs syarat 8.4.1), gunakan `composer install --ignore-platform-reqs`.

## How to Verify

```powershell
php artisan migrate:fresh --seed     # 18 tabel terbentuk dan seeder berjalan tanpa error
php artisan model:show Product       # daftar relasi Eloquent pada model Product
php artisan tinker                   # hitung baris tiap tabel (db:show butuh ekstensi intl)
```

Contoh perintah di dalam tinker untuk menghitung isi tabel:

```php
collect(['users','profiles','addresses','categories','brands','products','product_images','tags','product_tag','specification_types','product_specifications','compatibility_types','compatibility_rules','compatibilities','orders','order_items','payments','reviews'])->mapWithKeys(fn($t) => [$t => \Illuminate\Support\Facades\DB::table($t)->count()])
```

## Isi Database

18 tabel: `users`, `profiles`, `addresses`, `categories`, `brands`, `products`, `product_images`, `tags`, `product_tag`, `specification_types`, `product_specifications`, `compatibility_types`, `compatibility_rules`, `compatibilities`, `orders`, `order_items`, `payments`, `reviews`.

Jenis relasi yang dipakai: One-to-One, One-to-Many, Many-to-Many (dengan dan tanpa data pivot), Many-to-Many self-referencing, dan Has-Many-Through.

## Akun Seeder

| Peran | Email | Password |
| --- | --- | --- |
| Admin | admin@partshub.test | password |
| Seller | seller1@partshub.test, seller2@partshub.test | password |
| Buyer | buyer1@partshub.test sampai buyer3@partshub.test | password |

## Dokumentasi

- ERD dan ringkasan relasi: [docs/database/erd.md](docs/database/erd.md)
- Laporan progres: [docs/progress/P01-database-design.md](docs/progress/P01-database-design.md)
- Panduan fork dan PR: [docs/FORK_GUIDE.md](docs/FORK_GUIDE.md)
