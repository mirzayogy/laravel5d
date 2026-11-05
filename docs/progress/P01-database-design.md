# P01: Database Design and Table Relationships

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-25 |
| **Completed** | 2026-10-27 |
| **Branch** | `feature/p01-database-design` |
| **Pull request** | [#47](https://github.com/mirzayogy/laravel5d/pull/47) |

## Goal
Design and implement the database of the **CafeOrder** cafe menu ordering system, focusing on table relationships with Eloquent: One-to-One, One-to-Many, Many-to-Many (with pivot data), and Has-Many-Through.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Database design, ERD, and README | ✅ Done | 2026-10-25 | [erd.md](../database/erd.md), [README.md](../../README.md) |
| J2 | Progress report | ✅ Done | 2026-10-25 | [branch commits](https://github.com/PabloExelen/laravel5c/commits/feature/p01-database-design) |
| J3 | Migrations | ✅ Done | 2026-10-27 | [database/migrations](https://github.com/PabloExelen/laravel5c/tree/feature/p01-database-design/database/migrations) |
| J4 | Models and relationships | ✅ Done | 2026-10-27 | [app/Models](https://github.com/PabloExelen/laravel5c/tree/feature/p01-database-design/app/Models) |
| J5 | Factories and seeders | ✅ Done | 2026-10-27 | [database/factories](https://github.com/PabloExelen/laravel5c/tree/feature/p01-database-design/database/factories), [database/seeders](https://github.com/PabloExelen/laravel5c/tree/feature/p01-database-design/database/seeders) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Database design, ERD, and README
- **Status:** ✅ Done, 2026-10-25
- **What:** 8 tables covering every relationship type. `order_items` is the pivot between orders and menus and carries `quantity` and `unit_price`. The root README describes the project and contains the same Mermaid diagram as `erd.md`.
- **Proof:** [`docs/database/erd.md`](../database/erd.md), [`README.md`](../../README.md), [branch commits](https://github.com/PabloExelen/laravel5c/commits/feature/p01-database-design)
- **Verified:** the Mermaid diagram renders on the GitHub pages of `README.md` and `erd.md`.

### J2: Progress report
- **Status:** ✅ Done, 2026-10-25
- **What:** This file, recording phase P01 with its jobs, status, and proof.
- **Proof:** [`docs/progress/P01-database-design.md`](P01-database-design.md), [branch commits](https://github.com/PabloExelen/laravel5c/commits/feature/p01-database-design)

### J3: Migrations
- **Status:** ✅ Done, 2026-10-27
- **What:** Eight migrations: seven new tables (`categories`, `tags`, `profiles`, `menus`, `menu_tag`, `orders`, `order_items`) plus one migration that adds `role` to the existing `users` table. Foreign keys use cascade or restrict rules as described in the ERD design notes. `menu_tag` uses a composite primary key, and `order_items` has a unique constraint on (`order_id`, `menu_id`).
- **Proof:** [`database/migrations`](https://github.com/PabloExelen/laravel5c/tree/feature/p01-database-design/database/migrations), [branch commits](https://github.com/PabloExelen/laravel5c/commits/feature/p01-database-design)
- **Verified:** `php artisan migrate:fresh --seed` runs without errors, and `php artisan migrate:status` lists all 12 migrations as `Ran`.

### J4: Models and relationships
- **Status:** ✅ Done, 2026-10-27
- **What:** Six new models plus relationships added to `User`: `hasOne`, `hasMany`, `belongsTo`, `belongsToMany` (with `withPivot` for `quantity` and `unit_price`), and `hasManyThrough` (`Category` → `OrderItem` through `Menu`, `User` → `OrderItem` through `Order`), with casts for prices, booleans, and dates. `Order::total()` computes the order total from its items.
- **Proof:** [`app/Models`](https://github.com/PabloExelen/laravel5c/tree/feature/p01-database-design/app/Models), [branch commits](https://github.com/PabloExelen/laravel5c/commits/feature/p01-database-design)
- **Verified:** through tinker on the seeded data, including the profile of a customer, the pivot data of an order item, the tags of a menu, and the has-many-through counts.
- **Not done:** automated tests (planned for the next phase).

### J5: Factories and seeders
- **Status:** ✅ Done, 2026-10-27
- **What:** Factories for all new models, `CategorySeeder` (6 categories), and a `DatabaseSeeder` that keeps the lecturer's `PeriodeSeeder` call and creates an admin, a cashier, three customers with a profile each, five tags, two menus per category with tags, and two orders per customer with three items each. Item prices are copied from the menu price.
- **Proof:** [`database/factories`](https://github.com/PabloExelen/laravel5c/tree/feature/p01-database-design/database/factories), [`database/seeders`](https://github.com/PabloExelen/laravel5c/tree/feature/p01-database-design/database/seeders), [branch commits](https://github.com/PabloExelen/laravel5c/commits/feature/p01-database-design)

---

## Next
Phase P02 (`P02-application.md`) builds the application on top of this database.

Reference jobs in commit messages, for example `P01-J3: add menus migration`.
