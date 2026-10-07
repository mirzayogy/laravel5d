# P01: Database Design and Table Relationship

| | |
|---|---|
| **Name** | Aisha Nazela |
| **NPM** | 2410010357 |
| **Class** | TI 5C REG BJB |
| **Project** | Recipe App |
| **Status** | ✅ Done |
| **Branch** | `feature/database-relations` |
| **Pull request** |  |

## Goal
Design and implement the relational database schema for the **Recipe App**, covering all required Eloquent relationship types: One-to-One, One-to-Many, Many-to-Many, Many-to-Many (with pivot data), and Has-Many-Through.

## Jobs
| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Database Design & Mermaid ERD | ✅ Done | 2026-10-02 | [`docs/database/erd.md`](../database/erd.md) |
| J2 | Migrations (6 Tables + 2 Pivot Tables) | ✅ Done | 2026-10-02 | [`database/migrations`](../../database/migrations) |
| J3 | Eloquent Models & Relationships | ✅ Done | 2026-10-02 | [`app/Models`](../../app/Models) |
| J4 | Model Factories & Seeders | ✅ Done | 2026-10-03 | [`database/factories`](../../database/factories), [`database/seeders`](../../database/seeders) |
| J5 | Documentation & Verification | ✅ Done | 2026-10-03 | [`README.md`](../../README.md) |

---

### J1: Database Design and ERD
- **Status:** ✅ Done
- **Summary:** Engineered a relational architecture for the Recipe App comprising 6 entity tables (`users`, `profiles`, `categories`, `recipes`, `ingredients`, `reviews`) and 2 pivot tables (`favorites`, `recipe_ingredient`) with clearly defined foreign keys and rules.
- **Proof:** [`docs/database/erd.md`](../database/erd.md)

### J2: Migrations
- **Status:** ✅ Done
- **Summary:** Created migration files equipped with proper foreign key constraints, `cascadeOnDelete`, `restrictOnDelete`, and unique compound indexes (preventing duplicate favorites per user/recipe).
- **Proof:** [`database/migrations`](../../database/migrations)
- **Verification:** Command `php artisan migrate:fresh --seed` executes with 0 errors.

### J3: Models and Relationships
- **Status:** ✅ Done
- **Summary:** Implemented Eloquent models: `User`, `Profile`, `Category`, `Recipe`, `Ingredient`, `Review`. Configured all core relationships including `hasOne`, `hasMany`, `belongsTo`, `belongsToMany` (with pivot attributes), and `hasManyThrough`.
- **Proof:** [`app/Models`](../../app/Models)

### J4: Factories and Seeders
- **Status:** ✅ Done
- **Summary:** Built model factories using Faker definitions and populated seeders with default categories, realistic culinary recipes, ingredients, user profiles, and interactive pivot data.
- **Proof:** [`database/seeders/DatabaseSeeder.php`](../../database/seeders/DatabaseSeeder.php)

### J5: Documentation and Verification
- **Status:** ✅ Done
- **Summary:** Updated project progress documentation, ensured code styling standards, and verified full execution of database refresh and seeding workflows.