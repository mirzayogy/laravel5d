# Laporan Progres P01 — Database Design & Table Relationships

**Sistem**: Laundry Express  
**Mahasiswa**: Muhammad Ixmal Alimudin (NPM: 2410010280 / Kelas: TI 5D REG BJB)  

---

## 📌 Rincian Pekerjaan Phase 1 (P01)

| Job ID | Deskripsi Pekerjaan | File Terkait | Status |
|---|---|---|---|
| **J1** | Merancang struktur 10 tabel database & ERD | [`docs/database/erd.md`](../database/erd.md) | ✅ Selesai |
| **J2** | Membuat file migrasi database Laravel | `database/migrations/*` | ✅ Selesai |
| **J3** | Membuat Eloquent Models & menentukan relasi (1:1, 1:N, N:M, Has-Many-Through) | `app/Models/*` | ✅ Selesai |
| **J4** | Membuat Model Factories & Database Seeder | `database/factories/*`, `database/seeders/*` | ✅ Selesai |
| **J5** | Membuat Pengujian Otomatis (Automated Feature Testing) | `tests/Feature/DatabaseRelationshipsTest.php` | ✅ Selesai |

---

## 🧪 Hasil Pengujian (Test Results)

Pengujian dilakukan menggunakan PHPUnit dengan perintah:
```bash
php artisan test --filter=DatabaseRelationshipsTest
```

**Status**: Passed (10/10 tests, 23 assertions, 0 failures).
