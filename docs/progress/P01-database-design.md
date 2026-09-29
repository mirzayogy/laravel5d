# P01: Database Design and Table Relationships

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-09-29 |
| **Completed** | 2026-09-29 |
| **Branch** | `feature/database-relations` |
| **Pull request** | <https://github.com/mirzayogy/laravel5d/pull/1> |

## Goal
Design and implement the database of the **Habitude** habit tracker, focusing on table relationships with Eloquent: One-to-One, One-to-Many, Many-to-Many (with pivot data), and Has-Many-Through.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Database design and ERD | ✅ Done | 2026-09-29 | [erd.md](../database/erd.md) |
| J2 | Migrations | ✅ Done | 2026-09-29 | [commit e863fb3](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/commit/e863fb3656ede7982b52f7a3216d554ad8cda556) |
| J3 | Models and relationships | ✅ Done | 2026-09-29 | [commit e863fb3](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/commit/e863fb3656ede7982b52f7a3216d554ad8cda556) |
| J4 | Factories and seeders | ✅ Done | 2026-09-29 | [commit e863fb3](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/commit/e863fb3656ede7982b52f7a3216d554ad8cda556) |
| J5 | README and fork guide | ✅ Done | 2026-09-29 | [commit 24fc771](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/commit/24fc771d49f3ab6a62214d21a1ac31bce6e70c5c) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Database design and ERD
- **Status:** ✅ Done, 2026-09-29
- **What:** 11 tables covering every relationship type. Mood and journal were added to the original design; `journal_entries.mood_entry_id` is nullable and unique.
- **Proof:** [`docs/database/erd.md`](../database/erd.md)

### J2: Migrations
- **Status:** ✅ Done, 2026-09-29
- **What:** Nine table migrations plus two pivot migrations (`habit_tag`, `achievement_user`), with foreign keys, cascade and restrict rules, and unique constraints (one log per habit per day, one mood per user per day).
- **Proof:** [`database/migrations`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/database-relations/database/migrations), [commit e863fb3](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/commit/e863fb3656ede7982b52f7a3216d554ad8cda556)
- **Verified:** `php artisan migrate:fresh --seed` runs without errors.

### J3: Models and relationships
- **Status:** ✅ Done, 2026-09-29
- **What:** Nine new models plus relationships added to `User`: `hasOne`, `hasMany`, `belongsTo`, `belongsToMany` (with `withPivot`), and `hasManyThrough`, with casts for dates, booleans, and JSON.
- **Proof:** [`app/Models`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/database-relations/app/Models), [commit e863fb3](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/commit/e863fb3656ede7982b52f7a3216d554ad8cda556)
- **Verified:** through tinker on the seeded user: 8 habits, 112 logs through habits, 14 moods, 5 journals, 1 achievement with `earned_at`.
- **Not done:** automated tests.

### J4: Factories and seeders
- **Status:** ✅ Done, 2026-09-29
- **What:** Factories for all new models, `CategorySeeder` (8 themes), `AchievementSeeder` (5 badges), and a `DatabaseSeeder` that creates `test@example.com` with 14 days of sample data.
- **Proof:** [`database/factories`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/database-relations/database/factories), [`database/seeders`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/database-relations/database/seeders), [commit e863fb3](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/commit/e863fb3656ede7982b52f7a3216d554ad8cda556)

### J5: README and fork guide
- **Status:** ✅ Done, 2026-09-29
- **What:** English root README with author details and MIT license, plus a fork and pull request guide for Windows and macOS (in Indonesian).
- **Proof:** [`README.md`](../../README.md), [`docs/guides/fork-guide.md`](../guides/fork-guide.md), commits [e863fb3](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/commit/e863fb3656ede7982b52f7a3216d554ad8cda556) and [24fc771](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/commit/24fc771d49f3ab6a62214d21a1ac31bce6e70c5c)
- **Not done:** the fork guide has not been tested on Windows.

---

## Next
Later phases get their own file in this folder, for example `P02-authentication-and-habits.md`, each with its own jobs (J1, J2, ...).

Reference jobs in commit messages, for example `P01-J3: add Habit relationships`.
