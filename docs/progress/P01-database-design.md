# P01: Database Design and Table Relationships

|                  |                                                                 |
| ---------------- | --------------------------------------------------------------- |
| **Status**       | ✅ Done                                                          |
| **Started**      | 2026-10-06                                                      |
| **Completed**    | 2026-10-31                                                      |
| **Branch**       | `feature/database-relations`                                    |
| **Pull request** | Opened against `mirzayogy/laravel5d:main` from `Hersa7/PBOLaravel5c` |

## Goal

Design and implement the database of **GameBook**, a gaming session booking application, focusing on table relationships with Eloquent: One-to-One, One-to-Many, Many-to-Many (with pivot data), and Has-Many-Through.

## Jobs

| Code | Job                      | Status | Completed  | Proof |
| ---- | ------------------------ | ------ | ---------- | ----- |
| J1   | Database design and ERD  | ✅ Done | 2026-10-31 | [`docs/database/erd.md`](https://github.com/Hersa7/PBOLaravel5c/blob/feature/database-relations/docs/database/erd.md) |
| J2   | Migrations               | ✅ Done | 2026-10-31 | [`database/migrations`](https://github.com/Hersa7/PBOLaravel5c/tree/feature/database-relations/database/migrations) · commit [`f87a366`](https://github.com/Hersa7/PBOLaravel5c/commit/f87a366) |
| J3   | Models and relationships | ✅ Done | 2026-10-31 | [`app/Models`](https://github.com/Hersa7/PBOLaravel5c/tree/feature/database-relations/app/Models) · commit [`6357725`](https://github.com/Hersa7/PBOLaravel5c/commit/6357725) |
| J4   | Seeders                  | ✅ Done | 2026-10-31 | [`database/seeders`](https://github.com/Hersa7/PBOLaravel5c/tree/feature/database-relations/database/seeders) · commit [`32b2259`](https://github.com/Hersa7/PBOLaravel5c/commit/32b2259) |
| J5   | README                   | ✅ Done | 2026-10-31 | [`README.md`](https://github.com/Hersa7/PBOLaravel5c/blob/feature/database-relations/README.md) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Database design and ERD
- **Status:** ✅ Done, 2026-10-31
- **What:** Design of 11 tables (10 new tables plus the default `users` table) covering every relationship type, drawn as a Mermaid ERD with a relationship summary table.
- **Proof:** [`docs/database/erd.md`](https://github.com/Hersa7/PBOLaravel5c/blob/feature/database-relations/docs/database/erd.md)

### J2: Migrations
- **Status:** ✅ Done, 2026-10-31
- **What:** Eight table migrations plus two pivot migrations (`game_station`, `addon_booking`), with foreign keys, cascade and restrict rules, and unique constraints.
- **Proof:** [`database/migrations`](https://github.com/Hersa7/PBOLaravel5c/tree/feature/database-relations/database/migrations)
- **Verified:** `php artisan migrate:fresh --seed` runs without errors on MySQL.

### J3: Models and relationships
- **Status:** ✅ Done, 2026-10-31
- **What:** Eight new models plus relationships added to `User`: `hasOne`, `hasMany`, `belongsTo`, `belongsToMany` (with `withPivot`), and `hasManyThrough`.
- **Proof:** [`app/Models`](https://github.com/Hersa7/PBOLaravel5c/tree/feature/database-relations/app/Models)
- **Verified:** relationships checked through Tinker on the seeded data:

| Check | Result |
| ----- | ------ |
| `User::first()->profile` | profile of the first user |
| `User::first()->bookings->count()` | 2 |
| `Station::first()->games->pluck('title')` | Valorant, Dota 2 |
| `Booking::first()->payment` | QRIS payment, 30000 |
| `Booking::first()->addons->first()->pivot->quantity` | 2 |
| `Platform::first()->bookings->count()` | 1 |
| `User::first()->payments->count()` | 2 |

### J4: Seeders
- **Status:** ✅ Done, 2026-10-31
- **What:** `DatabaseSeeder` creates 4 platforms, 5 stations, 5 games, 3 add-ons, 2 users with profiles, 3 bookings with payments, and 2 reviews.
- **Proof:** [`database/seeders/DatabaseSeeder.php`](https://github.com/Hersa7/PBOLaravel5c/blob/feature/database-relations/database/seeders/DatabaseSeeder.php)

### J5: README
- **Status:** ✅ Done, 2026-10-31
- **What:** Root README with author details, a project description, and setup steps.
- **Proof:** [`README.md`](https://github.com/Hersa7/PBOLaravel5c/blob/feature/database-relations/README.md)

## Not done yet

- Factories
- Automated tests
- Authentication, CRUD pages, and booking screens (planned for the next phases)

## Next

P02: authentication and CRUD pages (file `docs/progress/P02-...md`).
