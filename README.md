# ServiceHub - Sistem Manajemen Bengkel & Servis Kendaraan

Proyek Praktikum Pemrograman Berorientasi Objek 2 (PBO 2) - Program Studi Teknik Informatika, Fakultas Teknologi Informasi, Universitas Islam Kalimantan Muhammad Arsyad Al Banjari.

---

## 👨‍💻 Identitas Mahasiswa
- **Nama Mahasiswa**: Muhammad Khairil Ilham
- **NPM**: 2410010205
- **Kelas**: TI 5D REG BJB
- **Mata Kuliah**: Pemrograman Berorientasi Objek 2 (Laravel)
- **Dosen Pengampu**: Mirza Yogy Kurniawan
- **Repositori Dosen (Upstream)**: [mirzayogy/laravel5d](https://github.com/mirzayogy/laravel5d)

---

## 📌 Deskripsi Proyek
**ServiceHub** adalah aplikasi web berbasis Laravel untuk pengelolaan operasional bengkel modern, meliputi:
- **Manajemen Profil Pengguna & Pelanggan** (Customer Profiles)
- **Registrasi & Data Kendaraan** (Kendaraan milik pelanggan)
- **Booking Jadwal Servis & Penugasan Mekanik**
- **Laporan Inspeksi Fisik Awal Check-In Kendaraan** (Odometer, rem, ban, aki)
- **Paket Layanan Servis & Kategori Servis**
- **Katalog & Pemakaian Suku Cadang (Spare Parts)**
- **Klaim Promo / Voucher Diskon**
- **Faktur & Pembayaran Resmi (Invoices)**
- **Ulasan & Penilaian Layanan (Customer Reviews)**

---

## 🗄️ Relasi Tabel (Eloquent Relationships)

Proyek ini mengimplementasikan seluruh ragam relasi Eloquent:
1. **One-to-One (1:1)**:
   - `User` ⟷ `UserProfile`
   - `ServiceBooking` ⟷ `InspectionReport`
   - `ServiceBooking` ⟷ `Invoice`
2. **One-to-Many (1:N)**:
   - `User` ⟶ `Vehicle`
   - `Vehicle` ⟶ `ServiceBooking`
   - `ServiceCategory` ⟶ `ServicePackage`
   - `Mechanic` ⟶ `ServiceBooking`
   - `ServiceBooking` ⟶ `ServiceReview`
3. **Many-to-Many (N:M with Pivot)**:
   - `ServiceBooking` ⟷ `ServicePackage` (pivot: `package_price`, `technician_notes`)
   - `ServiceBooking` ⟷ `SparePart` (pivot: `quantity`, `unit_price`, `subtotal_price`)
   - `User` ⟷ `Promotion` (pivot: `discount_applied`, `used_at`)
4. **Has-Many-Through**:
   - `User` ⟶ `ServiceBooking` (through `Vehicle`)

Dokumentasi lengkap ERD dan diagram Mermaid dapat dilihat di:
👉 [`docs/database/erd.md`](docs/database/erd.md)

---

## 🚀 Panduan Menjalankan Proyek

### 1. Kloning & Persiapan Lingkungan
```bash
git clone https://github.com/muhammadiril56/laravel5d.git
cd laravel5d
```

### 2. Instalasi Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Eksekusi Migrasi & Data Seeder
```bash
php artisan migrate:fresh --seed
```

### 5. Jalankan Automated Tests
```bash
php artisan test --filter=DatabaseRelationsTest
```

### 6. Menjalankan Server Lokal
```bash
php artisan serve
```
Buka browser pada alamat: `http://localhost:8000`

---

## 📋 Progres Tugas
Laporan tahapan pengerjaan tugas dicatat pada:
👉 [`docs/progress/P01-database-design.md`](docs/progress/P01-database-design.md)
