# Database Design — Entity Relationship Diagram (FishMarket Penjualan Ikan)

Dokumen ini menjelaskan skema database aplikasi **FishMarket — Penjualan Ikan** dan setiap relasi Eloquent yang digunakan dalam proyek.

## 1. Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o| PROFILES : "has one"
    USERS ||--o{ FISHES : "owns/sells (Produk Ikan)"
    USERS ||--o{ CUSTOMER_REVIEWS : "records review"
    USERS ||--o{ SALES_REPORTS : "writes report"
    USERS ||--o{ BADGE_USER : "earns"
    BADGES ||--o{ BADGE_USER : "awarded via"

    CATEGORIES ||--o{ FISHES : "groups"
    FISHES ||--o{ SALES_LOGS : "sales log"
    FISHES ||--o{ RESTOCK_REMINDERS : "restock reminder"
    FISHES ||--o{ FISH_TAG : "tagged via"
    TAGS ||--o{ FISH_TAG : "labels"

    CUSTOMER_REVIEWS ||--o| SALES_REPORTS : "may have"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
    }
    PROFILES {
        bigint id PK
        bigint user_id FK "unique"
        text bio
        string avatar
        string timezone
        int daily_goal
    }
    CATEGORIES {
        bigint id PK
        string name
        string slug UK
        string icon
        string color
    }
    FISHES {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        string name "Produk Ikan"
        text description
        int stock "Stok Produk"
        string unit "Satuan (ekor, kg, paket)"
        boolean is_active
    }
    SALES_LOGS {
        bigint id PK
        bigint fish_id FK
        date logged_date
        int value "Jumlah Terjual"
        text note
    }
    TAGS {
        bigint id PK
        string name UK "e.g. segar, terlaris, diskon"
    }
    FISH_TAG {
        bigint fish_id FK
        bigint tag_id FK
    }
    RESTOCK_REMINDERS {
        bigint id PK
        bigint fish_id FK
        time remind_at
        json days_of_week
        boolean is_enabled
    }
    CUSTOMER_REVIEWS {
        bigint id PK
        bigint user_id FK
        date entry_date
        tinyint rating "1-5 (Kepuasan Pembeli)"
        string note
    }
    SALES_REPORTS {
        bigint id PK
        bigint user_id FK
        bigint customer_review_id FK "nullable"
        date entry_date
        string title "Judul Laporan Penjualan"
        text content "Detail Laporan"
    }
    BADGES {
        bigint id PK
        string name
        text description
        string criteria
    }
    BADGE_USER {
        bigint user_id FK
        bigint badge_id FK
        timestamp earned_at
    }
```

## 2. Ringkasan Relasi Eloquent

| Tipe Relasi | Pemetaan Objek Penjualan Ikan | Model Eloquent |
|---|---|---|
| One-to-One | Profil Penjual ↔ User | `User` ↔ `Profile` (`hasOne` / `belongsTo`) |
| One-to-One (optional) | Catatan Laporan Penjualan ↔ Ulasan Pembeli | `CustomerReview` ↔ `SalesReport` (`hasOne` / `belongsTo`) |
| One-to-Many | Penjual → Katalog Produk Ikan | `User` → `Fish` (`hasMany` / `belongsTo`) |
| One-to-Many | Penjual → Ulasan Pembeli Harian | `User` → `CustomerReview` (`hasMany` / `belongsTo`) |
| One-to-Many | Penjual → Laporan Penjualan Harian | `User` → `SalesReport` (`hasMany` / `belongsTo`) |
| One-to-Many | Kategori Ikan → Produk Ikan | `Category` → `Fish` (`hasMany` / `belongsTo`) |
| One-to-Many | Produk Ikan → Log Penjualan Harian | `Fish` → `SalesLog` (`hasMany` / `belongsTo`) |
| One-to-Many | Produk Ikan → Pengingat Restok Produk | `Fish` → `RestockReminder` (`hasMany` / `belongsTo`) |
| Many-to-Many | Produk Ikan ↔ Tag (*segar*, *diskon*, *terlaris*) | `Fish` ↔ `Tag` (pivot `fish_tag`, `belongsToMany`) |
| Many-to-Many + Pivot | Seller ↔ Lencana Pencapaian (`earned_at`) | `User` ↔ `Badge` (`belongsToMany` + `withPivot` via `badge_user`) |
| Has-Many-Through | Seller → Total Log Penjualan melalui Produk Ikan | `User` → `SalesLog` through `Fish` (`hasManyThrough`) |
| Has-Many-Through | Kategori Ikan → Log Penjualan melalui Produk Ikan | `Category` → `SalesLog` through `Fish` (`hasManyThrough`) |

## 3. Kategori Penjualan Ikan (Seeded)

Tabel `categories` diisi oleh seeder dengan 8 kategori produk ikan:

| Nama Kategori | Slug | Icon |
|---|---|---|
| Ikan Hias | `ikan-hias` | 🐠 |
| Ikan Konsumsi | `ikan-konsumsi` | 🐟 |
| Ikan Laut | `ikan-laut` | 🌊 |
| Ikan Tawar | `ikan-tawar` | 💧 |
| Bibit & Benih Ikan | `bibit-benih-ikan` | 🌱 |
| Pakan Ikan | `pakan-ikan` | 📦 |
| Perlengkapan Akuarium | `perlengkapan-akuarium` | 🫧 |
| Obat & Nutrisi Ikan | `obat-nutrisi-ikan` | 💊 |
