This document describes the database schema of **Kedai Kopi** and every Eloquent relationship used in the project.

## 1. Relationship Summary

| Type | Relationship | Eloquent |
| :--- | :--- | :--- |
| One-to-Many | `Kategori` → `Menu` | `hasMany` / `belongsTo` |
| One-to-Many | `Pelanggan` → `Pesanan` | `hasMany` / `belongsTo` |
| One-to-Many | `Karyawan` → `Pesanan` | `hasMany` / `belongsTo` |
| One-to-Many | `Pesanan` → `DetailPesanan` | `hasMany` / `belongsTo` |
| One-to-Many | `Menu` → `DetailPesanan` | `hasMany` / `belongsTo` |

## 2. Categories (seeded)

The `kategori` table is populated by a seeder with the built-in categories:

| Name | Slug |
| :--- | :--- |
| Coffee | `coffee` |
| Non Coffee | `non-coffee` |
| Tea | `tea` |
| Snack | `snack` |
| Main Course | `main-course` |
| Dessert | `dessert` |
| Seasonal | `seasonal` |
| Merchandise | `merchandise` |

## 3. Design Notes

- **Unique constraints:** `kategori.nama_kategori` and `menu.nama_menu` so duplicates are avoided in the coffee shop menu.
- **Order transaction:** `pesanan` links `pelanggan` and `karyawan` to log who placed and processed the order.
- **Detail item link:** `detail_pesanan.pesanan_id` connects ordered items directly to `pesanan`, ensuring items are preserved per transaction.
- **Cascade rules:** deleting a `pesanan` cascades to its `detail_pesanan` items. Deleting a `kategori` is restricted while `menu` items still reference it.
- **Totals & stock:** `detail_pesanan.subtotal` and `pesanan.total` use decimal precision, while `menu.stok` updates upon transaction.

## 4. Entity Relationship Diagram

```mermaid
erDiagram
    PELANGGAN {
        bigint id PK
        string nama
        string no_telepon
        string alamat
    }

    KARYAWAN {
        bigint id PK
        string nama
        string jabatan
        string no_telepon
    }

    KATEGORI {
        bigint id PK
        string nama_kategori UK
    }

    MENU {
        bigint id PK
        bigint kategori_id FK
        string nama_menu
        decimal harga
        int stok
        string status
    }

    PESANAN {
        bigint id PK
        bigint pelanggan_id FK
        bigint karyawan_id FK
        date tanggal
        decimal total
        string metode_pembayaran
    }

    DETAIL_PESANAN {
        bigint id PK
        bigint pesanan_id FK
        bigint menu_id FK
        int jumlah
        decimal harga
        decimal subtotal
    }

    KATEGORI ||--o{ MENU : memiliki
    PELANGGAN ||--o{ PESANAN : melakukan
    KARYAWAN ||--o{ PESANAN : menangani
    PESANAN ||--|{ DETAIL_PESANAN : memiliki
    MENU ||--o{ DETAIL_PESANAN : dipesan
