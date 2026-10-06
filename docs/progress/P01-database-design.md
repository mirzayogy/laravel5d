# P01: Database Design and Table Relationships (FishMarket Penjualan Ikan)

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-09-29 |
| **Completed** | 2026-10-06 |
| **Branch** | `feature/database-relations` |
| **Pull request** | <https://github.com/mirzayogy/laravel5d/pull/30> |

## Goal
Design and implement the database schema for **FishMarket — Aplikasi Penjualan Ikan**, focusing on Eloquent table relationships: One-to-One, One-to-Many, Many-to-Many (with pivot data), and Has-Many-Through.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Database design and ERD | ✅ Done | 2026-10-06 | [erd.md](../database/erd.md) |
| J2 | Migrations | ✅ Done | 2026-10-06 | [`database/migrations`](https://github.com/riezkyk/laravel5d/tree/feature/database-relations/database/migrations) |
| J3 | Models and relationships | ✅ Done | 2026-10-06 | [`app/Models`](https://github.com/riezkyk/laravel5d/tree/feature/database-relations/app/Models) |
| J4 | Factories and seeders | ✅ Done | 2026-10-06 | [`database/seeders`](https://github.com/riezkyk/laravel5d/tree/feature/database-relations/database/seeders) |
| J5 | README and fork guide | ✅ Done | 2026-10-06 | [`README.md`](../../README.md) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Database design and ERD
- **Status:** ✅ Done, 2026-10-06
- **What:** ERD for FishMarket Penjualan Ikan with 11 tables covering all relationship types (One-to-One, One-to-Many, Many-to-Many with pivot `earned_at`, Has-Many-Through).
- **Proof:** [`docs/database/erd.md`](../database/erd.md)

### J2: Migrations
- **Status:** ✅ Done, 2026-10-06
- **What:** Table migrations and pivot tables (`habit_tag`, `achievement_user`) with foreign keys, cascade rules, and unique constraints.
- **Proof:** [`database/migrations`](https://github.com/riezkyk/laravel5d/tree/feature/database-relations/database/migrations)
- **Verified:** `php artisan migrate:fresh --seed` runs cleanly.

### J3: Models and relationships
- **Status:** ✅ Done, 2026-10-06
- **What:** Eloquent models with relationship methods (`hasOne`, `hasMany`, `belongsTo`, `belongsToMany`, `hasManyThrough`).
- **Proof:** [`app/Models`](https://github.com/riezkyk/laravel5d/tree/feature/database-relations/app/Models), [`tests/Feature/DatabaseRelationsTest.php`](https://github.com/riezkyk/laravel5d/blob/feature/database-relations/tests/Feature/DatabaseRelationsTest.php)
- **Verified:** Automated tests passing: `php artisan test` (14 passed, 25 assertions).

### J4: Factories and seeders
- **Status:** ✅ Done, 2026-10-06
- **What:** `CategorySeeder` (8 fish categories), `AchievementSeeder` (5 seller badges), and `DatabaseSeeder` with sample fish products and sales logs.
- **Proof:** [`database/seeders`](https://github.com/riezkyk/laravel5d/tree/feature/database-relations/database/seeders)

### J5: README and fork guide
- **Status:** ✅ Done, 2026-10-06
- **What:** Root README and fork guide updated for Riezky Kurniawan (2410010564, TI 5D REG BJB).
- **Proof:** [`README.md`](../../README.md), [`docs/guides/fork-guide.md`](../guides/fork-guide.md)
