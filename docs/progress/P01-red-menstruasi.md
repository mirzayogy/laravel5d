# P01 — Red Menstruasi: Database & Relasi

- **Nama:** Fija Mushofaini
- **NPM:** 2410010375
- **Kelas:** TI 5D REG BJB
- **Branch:** `feature/2410010375`
- **Assignment:** 1

Aplikasi pelacak menstruasi (terinspirasi Flo): catat periode, gejala, mood, jurnal, log harian, obat, pengingat, serta prediksi haid, ovulasi, dan masa subur.

## Status Job

| Job | Deskripsi | Status | Bukti |
|---|---|---|---|
| J1 | ERD 11 tabel + ringkasan relasi Eloquent | ✅ Done | `docs/database/erd.md` |
| J2 | Migration 11 tabel (FK, unique, cascade) | ✅ Done | `database/migrations/2026_10_03_*` |
| J3 | Model + relasi (hasOne, hasMany, belongsTo, belongsToMany + pivot, hasManyThrough) | ✅ Done | `app/Models/`, `app/Enums/` |
| J4 | Factory semua model + `SymptomSeeder` | ✅ Done | `database/factories/`, `database/seeders/SymptomSeeder.php` |
| J5 | `CyclePredictionService` (prediksi haid, ovulasi, masa subur, fase siklus) | ✅ Done | `app/Services/CyclePredictionService.php` |
| J6 | Feature test (prediksi, relasi, seeder) | ✅ Done | `tests/Feature/` |
| J7 | Controller, route, dan tampilan | ⏳ Planned | — |

## Cara Verifikasi

```bash
composer install
php artisan migrate:fresh --seed
php artisan test --compact
```

Hasil: 15 test lulus (44 assertion).

## Catatan

- Prediksi, fase siklus, dan statistik dihitung dari data siklus, tidak disimpan di tabel.
- Fitur artikel edukasi, ekspor PDF, dan mode privasi sengaja tidak dibuat.
