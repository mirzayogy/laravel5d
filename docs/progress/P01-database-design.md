# P01: Database Design and Table Relationships

| | |
|---|---|
| **Status** | Done |
| **Started** | 2026-10-06 |
| **Completed** | 2026-10-06 |
| **Branch** | `feature/database-relations` |

## Goal

Design and build the database layer of Gantangan, a songbird contest registry, so that every relationship type covered in the course is used on a realistic domain: One-to-One, One-to-Many, Many-to-Many, Many-to-Many with pivot data, and Has-Many-Through.

## Jobs

| Code | Job                                                    | Status | Completed  | Proof                                                       |
|------|--------------------------------------------------------|--------|------------|-------------------------------------------------------------|
| J1   | Database design and ERD with Mermaid                   | Done   | 2026-10-06 | [docs/database/erd.md](../database/erd.md)                  |
| J2   | Migrations: 8 entity tables and 2 pivot tables         | Done   | 2026-10-06 | [database/migrations](../../database/migrations)            |
| J3   | Eloquent models and relationships                      | Done   | 2026-10-06 | [app/Models](../../app/Models)                              |
| J4   | Factories and seeders                                  | Done   | 2026-10-06 | [database/factories](../../database/factories), [database/seeders](../../database/seeders) |
| J5   | Automated relationship tests                           | Done   | 2026-10-06 | [tests/Feature/DatabaseRelationshipsTest.php](../../tests/Feature/DatabaseRelationshipsTest.php) |
| J6   | README with student identity and project overview      | Done   | 2026-10-06 | [README.md](../../README.md)                                |

---

### J1: Database design and ERD with Mermaid
- **Status:** Done, 2026-10-06
- **What:** Chose a songbird contest registry as the domain and designed 11 tables around it, including the default `users` table. The ERD, a relationship summary, a table reference, the seeded species and the design notes are in one document.
- **Proof:** [docs/database/erd.md](../database/erd.md)
- **Verified:** Every relationship in the ERD matches a method on a model and a foreign key in a migration.
- **Not done:** None.

### J2: Migrations
- **Status:** Done, 2026-10-06
- **What:** Ten migrations that create `profiles`, `species`, `birds`, `health_records`, `training_logs`, `organizers`, `judges`, `contests`, and the pivot tables `judge_species` and `bird_contest`. Foreign keys carry explicit cascade, restrict or set-null rules. `bird_contest` has a composite primary key and a unique entry number per contest.
- **Proof:** [database/migrations](../../database/migrations)
- **Verified:** `php artisan migrate:fresh` runs without errors, and `php artisan migrate:rollback --step=10` drops all ten new tables and keeps `users`.
- **Not done:** None.

### J3: Eloquent models and relationships
- **Status:** Done, 2026-10-06
- **What:** Nine models plus the `ContestEntry` pivot model, covering One-to-One (`User` and `Profile`), One-to-Many, a self-referencing One-to-Many (`Bird` sire and dam), Many-to-Many (`Judge` and `Species`), Many-to-Many with pivot data (`Bird` and `Contest`) and three Has-Many-Through relationships. The existing `User` model was extended with its relationships.
- **Proof:** [app/Models](../../app/Models)
- **Verified:** Each relationship is exercised in the automated tests (J5).
- **Not done:** None.

### J4: Factories and seeders
- **Status:** Done, 2026-10-06
- **What:** A factory for every model that has one, and four seeders called from `DatabaseSeeder`: species, judges (with their certified species), owners (profiles, birds, health records, training logs, pedigree) and contests (organizers, contests, and birds registered with scores and ranks).
- **Proof:** [database/factories](../../database/factories), [database/seeders](../../database/seeders)
- **Verified:** `php artisan migrate:fresh --seed` runs without errors. The seeder was run 25 times in a row without failures. The demo account `test@example.com` always has 1 profile, 6 birds, 18 health records and 24 training logs.
- **Not done:** None.

### J5: Automated relationship tests
- **Status:** Done, 2026-10-06
- **What:** 15 tests with 51 assertions. They cover every relationship type, the pivot data and its casts, the unique constraints, the cascade, restrict and set-null rules, and a consistency check of the seeded data.
- **Proof:** [tests/Feature/DatabaseRelationshipsTest.php](../../tests/Feature/DatabaseRelationshipsTest.php)
- **Verified:** `php artisan test` passes: 17 tests, which are the 15 new tests and the 2 default example tests.
- **Not done:** None.

### J6: README with student identity and project overview
- **Status:** Done, 2026-10-06
- **What:** The student name and NPM are at the top of the README, followed by a short overview of the project, links to the documentation and the commands to run it. The original Laravel README is kept below.
- **Proof:** [README.md](../../README.md)
- **Verified:** The README renders on GitHub and the links resolve.
- **Not done:** None.

## Next

Authentication, CRUD pages for birds and contest registration, and a results dashboard are planned for the next phases.
