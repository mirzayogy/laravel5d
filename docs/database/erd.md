# Entity Relationship Diagram (ERD) - ServiceHub

Sistem Manajemen Bengkel, Booking Servis Kendaraan, Suku Cadang, dan Mekanik.

## 1. Mermaid Diagram

```mermaid
erDiagram
    USERS ||--o| USER_PROFILES : "has one"
    USERS ||--o{ VEHICLES : "owns"
    USERS ||--o{ PROMOTION_USER : "has used"
    PROMOTIONS ||--o{ PROMOTION_USER : "applied to"

    VEHICLES ||--o{ SERVICE_BOOKINGS : "booked for"
    MECHANICS ||--o{ SERVICE_BOOKINGS : "assigned to"

    SERVICE_BOOKINGS ||--o| INSPECTION_REPORTS : "has inspection"
    SERVICE_BOOKINGS ||--o| INVOICES : "generates"
    SERVICE_BOOKINGS ||--o{ SERVICE_REVIEWS : "reviewed by"

    SERVICE_CATEGORIES ||--o{ SERVICE_PACKAGES : "contains"
    SERVICE_BOOKINGS ||--o{ BOOKING_SERVICE_PACKAGE : "includes"
    SERVICE_PACKAGES ||--o{ BOOKING_SERVICE_PACKAGE : "included in"

    SERVICE_BOOKINGS ||--o{ BOOKING_SPARE_PART : "requires"
    SPARE_PARTS ||--o{ BOOKING_SPARE_PART : "supplied in"

    USERS {
        bigint id PK
        string name
        string email
        timestamp email_verified_at
        string password
        string role
        timestamps created_at
    }

    USER_PROFILES {
        bigint id PK
        bigint user_id FK
        string phone_number
        text address
        string city
        string postal_code
        string avatar_url
        timestamps created_at
    }

    VEHICLES {
        bigint id PK
        bigint user_id FK
        string plate_number
        string brand
        string model
        integer production_year
        string color
        string transmission_type
        timestamps created_at
    }

    MECHANICS {
        bigint id PK
        string name
        string phone
        string specialization
        integer experience_years
        string status
        timestamps created_at
    }

    SERVICE_CATEGORIES {
        bigint id PK
        string name
        string slug
        text description
        timestamps created_at
    }

    SERVICE_PACKAGES {
        bigint id PK
        bigint service_category_id FK
        string name
        text description
        decimal base_price
        integer estimated_duration_minutes
        timestamps created_at
    }

    SPARE_PARTS {
        bigint id PK
        string part_code
        string name
        string brand
        decimal unit_price
        integer stock_quantity
        string compatibility
        timestamps created_at
    }

    PROMOTIONS {
        bigint id PK
        string promo_code
        string title
        decimal discount_percentage
        decimal max_discount_amount
        decimal min_transaction_amount
        date valid_until
        boolean is_active
        timestamps created_at
    }

    SERVICE_BOOKINGS {
        bigint id PK
        string booking_code
        bigint vehicle_id FK
        bigint mechanic_id FK
        date booking_date
        time booking_time
        string status
        text customer_notes
        timestamps created_at
    }

    BOOKING_SERVICE_PACKAGE {
        bigint id PK
        bigint service_booking_id FK
        bigint service_package_id FK
        decimal package_price
        text technician_notes
        timestamps created_at
    }

    BOOKING_SPARE_PART {
        bigint id PK
        bigint service_booking_id FK
        bigint spare_part_id FK
        integer quantity
        decimal unit_price
        decimal subtotal_price
        timestamps created_at
    }

    PROMOTION_USER {
        bigint id PK
        bigint promotion_id FK
        bigint user_id FK
        decimal discount_applied
        timestamp used_at
        timestamps created_at
    }

    INSPECTION_REPORTS {
        bigint id PK
        bigint service_booking_id FK
        integer odometer_km
        string fuel_level
        string brake_condition
        string tire_condition
        string battery_condition
        text inspector_notes
        timestamps created_at
    }

    INVOICES {
        bigint id PK
        string invoice_number
        bigint service_booking_id FK
        decimal total_services_amount
        decimal total_parts_amount
        decimal discount_amount
        decimal grand_total
        string payment_method
        string payment_status
        timestamp paid_at
        timestamps created_at
    }

    SERVICE_REVIEWS {
        bigint id PK
        bigint service_booking_id FK
        integer rating
        text review_text
        timestamps created_at
    }
```

---

## 2. Relasi Antar Tabel (Eloquent Relationships)

### A. One-to-One (1:1)
1. **`User` <-> `UserProfile`**
   - Seorang user memiliki tepat 1 profil data diri lengkap (telepon, alamat, dsb).
   - Relasi: `User::hasOne(UserProfile::class)` dan `UserProfile::belongsTo(User::class)`.
2. **`ServiceBooking` <-> `InspectionReport`**
   - Setiap booking servis memiliki 1 laporan inspeksi awal saat kendaraan tiba di bengkel (odometer, kondisi rem, ban, aki).
   - Relasi: `ServiceBooking::hasOne(InspectionReport::class)` dan `InspectionReport::belongsTo(ServiceBooking::class)`.
3. **`ServiceBooking` <-> `Invoice`**
   - Setiap booking servis menghasilkan 1 tagihan/faktur pembayaran resmi.
   - Relasi: `ServiceBooking::hasOne(Invoice::class)` dan `Invoice::belongsTo(ServiceBooking::class)`.

### B. One-to-Many (1:N)
1. **`User` -> `Vehicle`**
   - Seorang pelanggan bisa mendaftarkan beberapa kendaraan miliknya (mobil/motor).
   - Relasi: `User::hasMany(Vehicle::class)` dan `Vehicle::belongsTo(User::class)`.
2. **`Vehicle` -> `ServiceBooking`**
   - Sebuah kendaraan memiliki riwayat banyak sesi servis dari waktu ke waktu.
   - Relasi: `Vehicle::hasMany(ServiceBooking::class)` dan `ServiceBooking::belongsTo(Vehicle::class)`.
3. **`ServiceCategory` -> `ServicePackage`**
   - Satu kategori servis (misal: Servis Berkala, Kelistrikan, Rem & Kaki-kaki) membawahi beberapa paket servis.
   - Relasi: `ServiceCategory::hasMany(ServicePackage::class)` dan `ServicePackage::belongsTo(ServiceCategory::class)`.
4. **`Mechanic` -> `ServiceBooking`**
   - Satu mekanik dapat ditugaskan untuk menangani banyak pengerjaan booking servis.
   - Relasi: `Mechanic::hasMany(ServiceBooking::class)` dan `Mechanic::belongsTo(Mechanic::class)`.
5. **`ServiceBooking` -> `ServiceReview`**
   - Booking servis memiliki ulasan/feedback dari pelanggan.
   - Relasi: `ServiceBooking::hasMany(ServiceReview::class)` dan `ServiceReview::belongsTo(ServiceBooking::class)`.

### C. Many-to-Many (N:M) dengan Pivot Data
1. **`ServiceBooking` <-> `SparePart` (via `booking_spare_part`)**
   - Dalam satu servis bisa memakai beberapa spare part, dan satu jenis spare part bisa dipakai di banyak booking.
   - Pivot data: `quantity`, `unit_price`, `subtotal_price`.
   - Relasi: `ServiceBooking::belongsToMany(SparePart::class)->withPivot(...)`.
2. **`ServiceBooking` <-> `ServicePackage` (via `booking_service_package`)**
   - Satu booking servis dapat mengambil beberapa paket pengerjaan (misal: Tune-Up + Ganti Oli + Spooring).
   - Pivot data: `package_price`, `technician_notes`.
   - Relasi: `ServiceBooking::belongsToMany(ServicePackage::class)->withPivot(...)`.
3. **`User` <-> `Promotion` (via `promotion_user`)**
   - User dapat mengklaim/menggunakan banyak voucher promo, dan voucher dapat digunakan oleh banyak user.
   - Pivot data: `discount_applied`, `used_at`.
   - Relasi: `User::belongsToMany(Promotion::class)->withPivot(...)`.

### D. Has-Many-Through
1. **`User` -> `ServiceBooking` (through `Vehicle`)**
   - Mengambil seluruh riwayat pemesanan servis seorang user melalui relasi kendaraan yang dimilikinya.
   - Relasi: `User::hasManyThrough(ServiceBooking::class, Vehicle::class)`.
