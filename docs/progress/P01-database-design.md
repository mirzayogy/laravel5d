# P01 – Database Design (PartsHub)

| Item | Isi |
| --- | --- |
| Nama | Ahmad Zainal Febryan |
| NIM | 2410010414 |
| Kelas | TI 5C REG BJB |
| Proyek | PartsHub |
| Branch | `feature/database-relations` |

## Status Pekerjaan

| Job | Isi | Status | Bukti |
| --- | --- | --- | --- |
| J1 | Desain database dan ERD Mermaid | Selesai | `docs/database/erd.md` |
| J2 | Migration 18 tabel | Selesai | `database/migrations/` |
| J3 | Model Eloquent dan relasi | Selesai | `app/Models/` |
| J4 | Factory dan seeder | Selesai | `database/factories/`, `database/seeders/` |
| J5 | README, panduan fork, laporan progres | Selesai | `README.md`, `docs/` |

## Ringkasan

- **18 tabel**, termasuk 3 tabel pivot (`product_tag`, `product_specifications`, `order_items`) dan 1 pivot self-referencing (`compatibilities`).
- **Spesifikasi fleksibel:** komponen baru cukup menambah baris di `specification_types`, tanpa migration baru.
- **Kecocokan komponen:** `compatibility_rules` menyimpan logika per kategori, `compatibilities` menyimpan pasangan produk nyata beserta status kecocokannya.
- **Price snapshot:** `order_items.unit_price` menyimpan harga saat pembelian, sehingga pesanan lama tidak berubah ketika harga produk berubah.
- **Seller dan buyer:** keduanya berada di tabel `users`, dibedakan oleh kolom `role`.

## Relasi Eloquent

| Jenis | Relasi |
| --- | --- |
| One-to-One | `User` – `Profile`, `Order` – `Payment` |
| One-to-Many | `User` → `Address` / `Product` (seller) / `Order` (buyer) / `Review`; `Category` → `Product` / `SpecificationType`; `Brand` → `Product`; `Product` → `ProductImage` / `Review`; `CompatibilityType` → `CompatibilityRule`; `Address` → `Order` |
| Many-to-Many | `Product` ↔ `Tag` (pivot `product_tag`) |
| Many-to-Many + data pivot | `Product` ↔ `SpecificationType` (`value`); `Order` ↔ `Product` (`quantity`, `unit_price`, `subtotal`) |
| Many-to-Many self-referencing | `Product` ↔ `Product` lewat `compatibilities` (`compatibility_type_id`, `status`, `note`) |
| Has-Many-Through | `Category` → `OrderItem`, `Category` → `Review`, `User` (seller) → `OrderItem`, semuanya lewat `Product` |

## Factory dan Seeder

- Factory: `UserFactory` (state `seller()` dan `admin()`), `ProductFactory`, `AddressFactory`.
- Seeder berurutan: `CategorySeeder`, `BrandSeeder`, `SpecificationTypeSeeder`, `CompatibilitySeeder`, `UserSeeder`, `ProductSeeder`, `OrderSeeder`, semuanya dipanggil dari `DatabaseSeeder`.

## Hasil Seeding

| Tabel | Baris | Tabel | Baris |
| --- | --- | --- | --- |
| users | 6 | product_specifications | 116 |
| profiles | 6 | compatibility_types | 6 |
| addresses | 3 | compatibility_rules | 6 |
| categories | 8 | compatibilities | 16 |
| brands | 10 | orders | 6 |
| products | 32 | order_items | 18 |
| product_images | 64 | payments | 6 |
| tags | 7 | reviews | 16 |
| product_tag | 64 | specification_types | 29 |

## Cara Verifikasi

```powershell
php artisan migrate:fresh --seed
php artisan model:show Product
php artisan tinker
```

Hasil: semua migration dan seeder berstatus `DONE` tanpa error.

## Kendala

- `composer install` gagal karena PHP 8.4.0 belum memenuhi syarat 8.4.1, diselesaikan dengan `--ignore-platform-reqs`.
- Sisa stub `$table->timestamps();` di luar blok `Schema::create` membuat satu migration gagal, diperbaiki lalu dijalankan ulang dengan `migrate:fresh`.
- `Product::factory()` error karena trait `HasFactory` belum dipasang di model `Product`.
- `php artisan db:show` gagal karena ekstensi PHP `intl` belum aktif, jumlah baris dicek lewat `php artisan tinker`.
