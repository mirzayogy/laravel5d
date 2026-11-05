# ERD: Kost Area

Dokumen ini memuat rancangan database **lengkap** untuk Kost Area. Sebagian tabel sudah dibuat dan dijalankan (lihat tabel status di bawah), sisanya dikerjakan di tahap berikutnya.

## Diagram

Diagram Mermaid (otomatis tampil di GitHub).

```mermaid
erDiagram
    USERS ||--o{ KOSTS : "memiliki"
    USERS ||--o| TENANTS : "akun login"
    USERS ||--o{ PAYMENTS : "memverifikasi"
    KOSTS ||--o{ ROOMS : "punya"
    KOSTS }o--o{ FACILITIES : "facility_kost"
    ROOMS }o--o{ FACILITIES : "facility_room"
    KOSTS ||--o{ IMAGES : "punya foto"
    ROOMS ||--o{ IMAGES : "punya foto"
    MAINTENANCE_REQUESTS ||--o{ IMAGES : "punya foto"
    ROOMS ||--o{ BOOKINGS : "dipesan"
    TENANTS ||--o{ BOOKINGS : "melakukan"
    BOOKINGS ||--o| BOOKINGS : "diperpanjang oleh"
    BOOKINGS ||--o{ PAYMENTS : "ditagih lewat"
    ROOMS ||--o{ MAINTENANCE_REQUESTS : "mengalami"
    TENANTS ||--o{ MAINTENANCE_REQUESTS : "melaporkan"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin, owner, tenant (rencana)"
    }
    TENANTS {
        bigint id PK
        bigint user_id FK "nullable, akun login (rencana)"
        string name
        enum gender "laki-laki, perempuan"
        string phone
        string id_number UK
        string occupation
        text address "rencana"
        string emergency_contact "rencana"
        string avatar "rencana"
    }
    KOSTS {
        bigint id PK
        bigint user_id FK "pemilik"
        string name
        enum type "putra, putri"
        string address
        string city
        text description
        datetime deleted_at "nullable, soft delete (rencana)"
    }
    ROOMS {
        bigint id PK
        bigint kost_id FK
        string room_number "unik per kost"
        tinyint floor
        decimal size_m2
        int price_per_day
        int price_per_month
        enum status "available, occupied, maintenance"
        datetime deleted_at "nullable, soft delete (rencana)"
    }
    FACILITIES {
        bigint id PK
        string name UK
        enum scope "room, property (rencana)"
    }
    FACILITY_ROOM {
        bigint id PK
        bigint room_id FK
        bigint facility_id FK
    }
    FACILITY_KOST {
        bigint id PK
        bigint kost_id FK
        bigint facility_id FK
    }
    IMAGES {
        bigint id PK
        string imageable_type "Kost, Room, MaintenanceRequest"
        bigint imageable_id
        string path
        boolean is_cover
        int sort_order
    }
    BOOKINGS {
        bigint id PK
        bigint room_id FK
        bigint tenant_id FK
        bigint previous_booking_id FK "nullable, perpanjangan"
        string code UK
        enum rental_type "daily, monthly"
        date start_date
        date end_date
        int duration "hari atau bulan"
        int price_per_unit "tarif saat booking"
        int deposit_amount "0 untuk sewa harian"
        int total_price
        datetime expires_at "nullable, batas bayar saat pending"
        datetime checked_in_at "nullable"
        datetime checked_out_at "nullable"
        enum status "pending, confirmed, active, completed, cancelled"
    }
    PAYMENTS {
        bigint id PK
        bigint booking_id FK
        bigint verified_by FK "nullable, users"
        enum type "deposit, rent, late_fee, refund"
        int amount
        date due_date
        enum method "cash, transfer, qris, nullable"
        enum status "unpaid, waiting_verification, paid, overdue"
        string proof_path "nullable"
        text notes "nullable"
        datetime paid_at "nullable"
        datetime verified_at "nullable"
    }
    MAINTENANCE_REQUESTS {
        bigint id PK
        bigint room_id FK
        bigint tenant_id FK
        string title
        text description
        enum priority "low, medium, high, urgent"
        enum status "open, in_progress, resolved"
        datetime resolved_at "nullable"
    }
```

## Status implementasi

| Tabel | Status | Catatan |
|---|---|---|
| `users` | Sudah dibuat | Kolom `role` masih rencana |
| `kosts` | Sudah dibuat | `deleted_at` (soft delete) masih rencana |
| `rooms` | Sudah dibuat | `deleted_at` (soft delete) masih rencana |
| `facilities` | Sudah dibuat | Kolom `scope` masih rencana |
| `facility_room` | Sudah dibuat | |
| `tenants` | Sudah dibuat | `user_id`, `address`, `emergency_contact`, `avatar` masih rencana |
| `bookings` | Sudah dibuat | Lengkap |
| `payments` | Sudah dibuat | Lengkap |
| `facility_kost` | Rencana | Fasilitas milik kost (parkir, dapur bersama, laundry, CCTV) |
| `images` | Rencana | Foto kost, kamar, dan laporan kerusakan |
| `maintenance_requests` | Rencana | Laporan kerusakan dari penghuni |

## Ringkasan relasi

| Tabel A | Relasi | Tabel B | Keterangan |
|---|---|---|---|
| users | one-to-many | kosts | satu pemilik punya banyak kost (`kosts.user_id`) |
| users | one-to-one | tenants | akun login penghuni (`tenants.user_id`, rencana) |
| users | one-to-many | payments | pemilik yang memverifikasi (`payments.verified_by`) |
| kosts | one-to-many | rooms | satu kost punya banyak kamar (`rooms.kost_id`) |
| rooms | many-to-many | facilities | pivot `facility_room` |
| kosts | many-to-many | facilities | pivot `facility_kost` (rencana) |
| kosts, rooms, maintenance_requests | polymorphic one-to-many | images | `imageable_type` + `imageable_id` (rencana) |
| rooms | one-to-many | bookings | satu kamar dipesan berkali-kali pada periode berbeda |
| tenants | one-to-many | bookings | satu penyewa punya banyak booking |
| bookings | self-reference | bookings | perpanjangan lewat `previous_booking_id` |
| bookings | one-to-many | payments | deposit, sewa per bulan, denda, dan pengembalian deposit |
| rooms | one-to-many | maintenance_requests | kamar yang mengalami masalah (rencana) |
| tenants | one-to-many | maintenance_requests | penghuni yang melapor (rencana) |

## Jenis relasi yang dicakup

| Jenis | Contoh |
|---|---|
| One-to-One | `User` ↔ `Tenant` (rencana) |
| One-to-Many | `User` → `Kost`, `Kost` → `Room`, `Room` → `Booking`, `Tenant` → `Booking`, `Booking` → `Payment`, `Room` → `MaintenanceRequest` |
| Many-to-Many | `Room` ↔ `Facility`, `Kost` ↔ `Facility` (rencana) |
| Many-to-Many dengan data pivot | `Tenant` ↔ `Room` melalui `bookings` (jenis sewa, tanggal, durasi, tarif, deposit, status) |
| Has-Many-Through | `Kost` → `Booking` melalui `Room`, `User` (pemilik) → `Room` melalui `Kost` |
| Polymorphic One-to-Many | `Image` milik `Kost`, `Room`, atau `MaintenanceRequest` (rencana) |
| Self-referencing | `Booking` → `Booking` lewat `previous_booking_id` |

## Nilai kolom enum

| Kolom | Nilai |
|---|---|
| `users.role` | `admin`, `owner`, `tenant` |
| `kosts.type` | `putra`, `putri` |
| `tenants.gender` | `laki-laki`, `perempuan` |
| `rooms.status` | `available`, `occupied`, `maintenance` |
| `facilities.scope` | `room`, `property` |
| `bookings.rental_type` | `daily`, `monthly` |
| `bookings.status` | `pending`, `confirmed`, `active`, `completed`, `cancelled` |
| `payments.type` | `deposit`, `rent`, `late_fee`, `refund` |
| `payments.method` | `cash`, `transfer`, `qris` |
| `payments.status` | `unpaid`, `waiting_verification`, `paid`, `overdue` |
| `maintenance_requests.priority` | `low`, `medium`, `high`, `urgent` |
| `maintenance_requests.status` | `open`, `in_progress`, `resolved` |

## Aturan integritas

- Semua foreign key memakai `cascadeOnDelete`, kecuali `bookings.previous_booking_id` dan `payments.verified_by` yang menjadi `null` saat induknya dihapus.
- `rooms`: kombinasi `kost_id` + `room_number` unik.
- `facility_room` dan `facility_kost`: kombinasi pasangan id unik (tidak ada duplikat).
- `bookings.code`, `tenants.id_number`, dan `facilities.name` unik.
- Tarif pada `bookings.price_per_unit` disalin saat booking dibuat, sehingga perubahan harga kamar tidak memengaruhi booking lama.
- Kost dan kamar akan memakai *soft delete* agar riwayat booking dan pembayaran tetap utuh (rencana).