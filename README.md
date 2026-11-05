# Kost Area: Platform Manajemen dan Booking Kos

Kost Area adalah aplikasi web manajemen dan booking kos yang dibangun dengan **Laravel** dan **Blade**. Proyek ini merupakan tugas mata kuliah **PBO2**, dengan fokus pada perancangan dan implementasi **relasi antar tabel database** menggunakan Eloquent ORM. Studi kasusnya: satu pemilik mengelola dua kost, **Kost Area Putra** dan **Kost Area Putri**.

## Fitur yang Sudah Ada

- Pemilik dapat memiliki lebih dari satu kost
- Kost khusus putra atau khusus putri (tidak ada kost campur)
- Kamar dengan lantai, ukuran, harga sewa harian, harga sewa bulanan, dan status (`available`, `occupied`, `maintenance`)
- Fasilitas kamar (AC, WiFi, kamar mandi dalam, kasur, lemari) yang dipakai bersama oleh banyak kamar
- Data penyewa beserta jenis kelamin
- Booking kamar dengan sewa harian atau bulanan; tarif saat booking disimpan sehingga perubahan harga tidak memengaruhi booking lama
- Perpanjangan sewa (booking menunjuk booking sebelumnya), waktu check-in dan check-out
- Pembayaran bertipe deposit, sewa, denda, dan pengembalian deposit, dengan jatuh tempo
- Metode bayar tunai, transfer, atau QRIS, dengan bukti bayar dan verifikasi oleh pemilik
- Status pembayaran: Belum Dibayar, Menunggu Verifikasi, Lunas, dan Terlambat
- Halaman web untuk menampilkan data: Dashboard, Kost, Kamar, Fasilitas, Penyewa, Booking, dan Pembayaran

## Desain Database

Diagram ERD lengkap (semua tabel dan kolom, termasuk yang masih rencana), tabel status implementasi, dan penjelasan relasi ada di [`docs/erd.md`](docs/erd.md).

### Relasi yang Dicakup

| Jenis | Contoh |
|---|---|
| One-to-Many | `User` → `Kost`, `Kost` → `Room`, `Room` → `Booking`, `Tenant` → `Booking`, `Booking` → `Payment`, `User` (verifikator) → `Payment` |
| Many-to-Many | `Room` ↔ `Facility` (tabel pivot `facility_room`) |
| Self-referencing | `Booking` → `Booking` lewat `previous_booking_id` (perpanjangan sewa) |

### Aturan Bisnis

- Jenis kost (`kosts.type`) hanya `putra` atau `putri`. Semua kamar di sebuah kost mengikuti jenis kost-nya.
- Jenis kelamin penyewa (`tenants.gender`) dicatat agar penyewa hanya memesan kost yang sesuai. Data contoh sudah mengikuti aturan ini. Pemeriksaan otomatis saat membuat booking menyusul bersama form booking.
- `room_number` unik di dalam satu kost. `tenants.id_number` dan `facilities.name` unik.
- Satu kamar dapat dipesan berkali-kali pada periode berbeda. Satu penyewa dapat memiliki banyak booking.
- `bookings.rental_type` adalah `daily` atau `monthly`. `duration` dihitung dalam hari untuk `daily` dan bulan untuk `monthly`.
- Saat booking dibuat, tarif kamar disalin ke `bookings.price_per_unit`, lalu `total_price` = `price_per_unit` x `duration` dan `end_date` = `start_date` + `duration`.
- Sewa bulanan: ada satu pembayaran `deposit` dan satu pembayaran `rent` per bulan, masing-masing dengan `due_date` sendiri.
- Sewa harian: tanpa deposit (`deposit_amount` = 0), dan hanya satu pembayaran `rent` senilai `total_price`.
- Untuk transfer dan QRIS, penyewa mengunggah bukti bayar sehingga status menjadi `waiting_verification`. Setelah diverifikasi pemilik (`verified_by`), status menjadi `paid`. Pembayaran tunai dapat langsung dicatat `paid` oleh pemilik.
- Pembayaran `unpaid` yang melewati `due_date` berstatus `overdue`. Denda keterlambatan dicatat sebagai pembayaran bertipe `late_fee`.
- Pengembalian deposit dicatat sebagai pembayaran bertipe `refund`. Jika dipotong, alasannya dicatat di `payments.notes`.
- Perpanjangan sewa dibuat sebagai booking baru dengan `previous_booking_id` menunjuk booking sebelumnya.
- Semua foreign key memakai `cascadeOnDelete`, kecuali `previous_booking_id` dan `verified_by` yang menjadi `null` saat induknya dihapus.

Catatan: aturan seperti pembatalan otomatis booking yang tidak dibayar, penagihan otomatis, dan penandaan keterlambatan baru berupa rancangan struktur data. Prosesnya belum diprogram dan dikerjakan di tahap berikutnya.

## Teknologi yang Digunakan

- PHP 8.3+ dan Laravel
- Template Blade
- MySQL atau SQLite

## Memulai

```bash
# Clone repository
git clone https://github.com/Luchyana-Desi/laravel5d
cd laravel5d

# Install dependensi
composer install

# Atur environment
cp .env.example .env
php artisan key:generate

# Buat skema database dan isi data contoh
php artisan migrate:fresh --seed

# Jalankan server pengembangan
php artisan serve
```

Lalu buka <http://localhost:8000>.

Seeder (`KostAreaSeeder`) mengisi data contoh: 1 pemilik, 5 fasilitas, 2 kost (putra dan putri), 10 kamar, 6 penyewa (sesuai jenis kost), 6 booking (4 bulanan, 2 harian), dan 20 pembayaran dengan berbagai status.

## Halaman yang Tersedia

| Alamat | Isi |
|---|---|
| `/` | Dashboard ringkasan |
| `/kosts`, `/kosts/{id}` | Daftar dan detail kost |
| `/rooms`, `/rooms/{id}` | Daftar dan detail kamar |
| `/facilities`, `/facilities/{id}` | Daftar dan detail fasilitas |
| `/tenants`, `/tenants/{id}` | Daftar dan detail penyewa |
| `/bookings`, `/bookings/{id}` | Daftar dan detail booking |
| `/payments`, `/payments/{id}` | Daftar dan detail pembayaran |

## Struktur Proyek

```
app/Http/Controllers/   Controller (menampilkan data)
app/Models/             Model Eloquent dan relasinya
database/
  migrations/           Definisi tabel
  factories/            Pembuat data contoh
  seeders/              KostAreaSeeder dan DatabaseSeeder
resources/views/        Template Blade (layout, dashboard, dan halaman per tabel)
routes/web.php          Daftar route
docs/
  erd.md                Dokumentasi ERD dan relasi
  progress/             Laporan progres per fase
```

## Roadmap

- [x] Desain dan dokumentasi database
- [x] Migration, model, factory, dan seeder
- [x] Controller, route, dan tampilan data (Assignment 1)
- [ ] CRUD dengan template AdminLTE (Assignment 2)
- [ ] Fasilitas kost dan foto (kost, kamar)
- [ ] Laporan kerusakan
- [ ] Profil penghuni, autentikasi, dan akses berbasis peran
- [ ] Proses otomatis: pembatalan booking, penagihan, dan penandaan pembayaran terlambat

## Penulis

Luchyana Desi Safitri, NPM 2410010230

## Lisensi

Dirilis di bawah [Lisensi MIT](https://opensource.org/licenses/MIT).