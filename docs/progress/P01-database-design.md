# P01: Database Design and Table Relationships

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-04 |
| **Completed** | 2026-10-25 |
| **Branch** | `feature/p01-database-design` |
| **Pull request** | [#46](https://github.com/mirzayogy/laravel5d/pull/46) |

## Goal
Design and implement the database of the **Flagboard** CTF competition management platform, focusing on table relationships with Eloquent: One-to-One, One-to-Many, Many-to-Many (with pivot data), and Has-Many-Through.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Project README with ERD diagram | ✅ Done | 2026-10-04 | [commit 30a4b18](https://github.com/deadyiss/laravel5d/commit/30a4b18a709a39b514f7c7a994f0b2eba92fff1c) |
| J2 | ERD documentation and README sync | ✅ Done | 2026-10-25 | [erd.md](../database/erd.md), [branch commits](https://github.com/deadyiss/laravel5d/commits/feature/p01-database-design) |
| J3 | Progress report | ✅ Done | 2026-10-25 | [branch commits](https://github.com/deadyiss/laravel5d/commits/feature/p01-database-design) |
| J4 | Migrations | ✅ Done | 2026-10-25 | [database/migrations](https://github.com/deadyiss/laravel5d/tree/feature/p01-database-design/database/migrations), [branch commits](https://github.com/deadyiss/laravel5d/commits/feature/p01-database-design) |
| J5 | Models and relationships | ✅ Done | 2026-10-25 | [app/Models](https://github.com/deadyiss/laravel5d/tree/feature/p01-database-design/app/Models), [branch commits](https://github.com/deadyiss/laravel5d/commits/feature/p01-database-design) |
| J6 | Factories and seeders | ✅ Done | 2026-10-25 | [database/factories](https://github.com/deadyiss/laravel5d/tree/feature/p01-database-design/database/factories), [database/seeders](https://github.com/deadyiss/laravel5d/tree/feature/p01-database-design/database/seeders) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Project README with ERD diagram
- **Status:** ✅ Done, 2026-10-04
- **What:** English root README with project description, features, ERD diagram in Mermaid, relationship table, business rules, setup steps, and roadmap.
- **Proof:** [`README.md`](../../README.md), [commit 30a4b18](https://github.com/deadyiss/laravel5d/commit/30a4b18a709a39b514f7c7a994f0b2eba92fff1c)
- **Verified:** the Mermaid diagram renders on the GitHub page of `README.md`.

### J2: ERD documentation and README sync
- **Status:** ✅ Done, 2026-10-25
- **What:** `docs/database/erd.md` with the full ERD of 13 tables, the Eloquent relationship summary, the seeded categories, and design notes. Three pivot tables carry extra data: `competition_team` (`registered_at`), `hint_unlocks` (`unlocked_at`), and `submissions` (`is_correct`, `submitted_at`). Flags are stored as hashes only. The README diagram was updated to match `erd.md`, and the clone URL in the README was filled in.
- **Proof:** [`docs/database/erd.md`](../database/erd.md), [`README.md`](../../README.md), [branch commits](https://github.com/deadyiss/laravel5d/commits/feature/p01-database-design)

### J3: Progress report
- **Status:** ✅ Done, 2026-10-25
- **What:** This file, recording phase P01 with its jobs, status, and proof.
- **Proof:** [`docs/progress/P01-database-design.md`](P01-database-design.md), [branch commits](https://github.com/deadyiss/laravel5d/commits/feature/p01-database-design)

### J4: Migrations
- **Status:** ✅ Done, 2026-10-25
- **What:** 13 migrations: 12 new tables plus one migration that adds `role` and `team_id` to the existing `users` table. Foreign keys use cascade, null-on-delete, or restrict rules as described in the ERD design notes. Pivot tables use composite primary keys, and unique constraints cover emails, team names and invite codes, slugs, and category and tag names.
- **Proof:** [`database/migrations`](https://github.com/deadyiss/laravel5d/tree/feature/p01-database-design/database/migrations), [branch commits](https://github.com/deadyiss/laravel5d/commits/feature/p01-database-design)
- **Verified:** `php artisan migrate:fresh --seed` runs without errors, and `php artisan migrate:status` lists all 17 migrations as `Ran`.

### J5: Models and relationships
- **Status:** ✅ Done, 2026-10-25
- **What:** Nine new models plus relationships added to `User`: `hasOne`, `hasMany`, `belongsTo`, `belongsToMany` (with `withPivot` for `registered_at`, `unlocked_at`, and the submission columns), and `hasManyThrough` (`Competition` → `Submission` and `Category` → `Submission`, both through `Challenge`), with casts for dates and booleans. `Challenge` hides `flag_hash` from serialization.
- **Proof:** [`app/Models`](https://github.com/deadyiss/laravel5d/tree/feature/p01-database-design/app/Models), [branch commits](https://github.com/deadyiss/laravel5d/commits/feature/p01-database-design)
- **Verified:** through tinker on the seeded data, including the profile of a user, the competitions of a team, the pivot data of a hint unlock, and the has-many-through counts.
- **Not done:** automated tests.

### J6: Factories and seeders
- **Status:** ✅ Done, 2026-10-25
- **What:** Factories for all new models, `CategorySeeder` (6 categories), and a `DatabaseSeeder` that creates an admin, three teams with two players and a profile each, one running competition, one challenge per category with two hints and tags, sample submissions, hint unlocks, and writeups.
- **Proof:** [`database/factories`](https://github.com/deadyiss/laravel5d/tree/feature/p01-database-design/database/factories), [`database/seeders`](https://github.com/deadyiss/laravel5d/tree/feature/p01-database-design/database/seeders), [branch commits](https://github.com/deadyiss/laravel5d/commits/feature/p01-database-design)

---

## Next
Later phases get their own file in this folder, for example `P02-authentication-and-teams.md`, each with its own jobs (J1, J2, ...).

Reference jobs in commit messages, for example `P01-J4: add migrations for 13 tables`.
