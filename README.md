# Laundry Express — Sistem Informasi Manajemen Services Laundry

> **Tugas 1 (Assignment 1)**: Database Design and Table Relationships  
> **Mata Kuliah**: Pemrograman Berbasis Objek 2 (PBO 2) / Laravel Framework  
> **Dosen Pengampu**: Mirza Yogy Utama  

---

## 👤 Identitas Mahasiswa

| Informasi | Detail |
|---|---|
| **Nama** | Muhammad Ixmal Alimudin |
| **NPM** | 2410010280 |
| **Kelas** | TI 5D REG BJB |
| **Repositori Fork** | [ixmalUK/laravel5d](https://github.com/ixmalUK/laravel5d) |
| **Upstream Repositori** | [mirzayogy/laravel5d](https://github.com/mirzayogy/laravel5d) |

---

## 📌 Ringkasan Proyek

**Laundry Express** adalah aplikasi sistem informasi manajemen jasa laundry berbasis web yang dirancang menggunakan framework Laravel 11/12 untuk mengelola operasional bisnis laundry kiloan, satuan spesialis (bedcover, jas, gaun), hingga perawatan sepatu & tas. Sistem ini mengelola data pelanggan, katalog kategori & layanan, nomor rak penyimpanan pakaian, pendaftaran transaksi cucian (*laundry orders*), rincian item, transaksi pembayaran, riwayat perubahan status pengerjaan (*status logs*), hingga ulasan kepuasan pelanggan (*customer reviews*).

---

## 🗄️ Daftar Entitas & Tabel Database

Sistem ini memiliki **10 tabel database** yang saling terhubung:

1. **`users`**: Data pengguna (Admin, Staff Operator, Customer).
2. **`user_profiles`**: Profil detail pengguna (NIK, kontak darurat, catatan tambahan, avatar).
3. **`service_categories`**: Kategori layanan (Layanan Kiloan, Satuan & Spesialis, Sepatu & Tas).
4. **`service_items`**: Detail layanan (Cuci Komplit, Cuci Express, Bedcover, Deep Clean Sneakers).
5. **`storage_racks`**: Rak lokasi penyimpanan pakaian siap ambil (Kode Rak, Zona, Kapasitas).
6. **`laundry_orders`**: Transaksi penerimaan order laundry (Kode Order, Pelanggan, Rak, Total Berat, Total Bayar, Status).
7. **`order_items`**: Detail item rincian pesanan laundry (N:M pivot antara Order & ServiceItem).
8. **`payments`**: Catatan transaksi pembayaran order laundry (Kode Pembayaran, Metode, Status, Tanggal Bayar).
9. **`order_status_logs`**: Log historis pergeseran status pengerjaan laundry oleh Staff.
10. **`customer_reviews`**: Ulasan dan rating kepuasan pelanggan atas order laundry.

---

## 🔗 Matriks Relasi Eloquent

| Relasi | Model Asal | Model Tujuan | Jenis Relasi | Keterangan |
|---|---|---|---|---|
| 1 | `User` | `UserProfile` | **One-to-One (1:1)** | Profil pengguna terhubung 1:1 dengan User |
| 2 | `ServiceCategory` | `ServiceItem` | **One-to-Many (1:N)** | Kategori layanan membawahi banyak item service |
| 3 | `User` (Customer) | `LaundryOrder` | **One-to-Many (1:N)** | Pelanggan (*Customer*) memiliki banyak pesanan laundry |
| 4 | `StorageRack` | `LaundryOrder` | **One-to-Many (1:N)** | Rak penyimpanan menampung banyak pesanan laundry |
| 5 | `LaundryOrder` | `ServiceItem` | **Many-to-Many (N:M)** | Order berisi item service melalui `order_items` |
| 6 | `LaundryOrder` | `Payment` | **One-to-Many (1:N)** | Pesanan laundry memiliki catatan tagihan pembayaran |
| 7 | `User` (Customer) | `Payment` | **Has-Many-Through** | Mengakses pembayaran Pelanggan melalui pesanan LaundryOrder |
| 8 | `ServiceCategory` | `OrderItem` | **Has-Many-Through** | Mengakses rincian item order di Kategori melalui ServiceItem |
| 9 | `LaundryOrder` | `OrderStatusLog` | **One-to-Many (1:N)** | Pesanan laundry memiliki log historis perubahan status |
| 10 | `LaundryOrder` | `CustomerReview` | **One-to-One (1:1)** | Pesanan laundry memiliki 1 ulasan kepuasan dari pelanggan |

---

## 📄 Dokumentasi Terkait

- **Entity Relationship Diagram (ERD)**: [`docs/database/erd.md`](docs/database/erd.md)
- **Laporan Progres P01**: [`docs/progress/P01-database-design.md`](docs/progress/P01-database-design.md)
- **Panduan Fork & Pull Request**: [`docs/FORK_GUIDE.md`](docs/FORK_GUIDE.md)

---

## 🧪 Cara Verifikasi & Pengujian

Jalankan perintah berikut di terminal:

```bash
# 1. Jalankan migrasi dan seeder
php artisan migrate:fresh --seed

# 2. Jalankan pengujian otomatis untuk relasi database
php artisan test --filter=DatabaseRelationshipsTest
```
