# P01: Database Design and Table Relationships — LokalBites

| | |
|---|---|
| **Student** | Muhammad Abdi Nur Salam |
| **Student ID (NPM)** | 2410010482 |
| **Class** | TI 5C REG BJB |
| **Project** | **LokalBites** (Local MSME Food & Catering Platform) |
| **Status** | ✅ Done |
| **Branch** | `feature/database-relations` |
| **Pull Request** | <https://github.com/mirzayogy/laravel5d/pull/6> |

---

## Goal
Design and implement the relational database schema for the **LokalBites** platform using Laravel's Eloquent ORM. The implementation covers all primary relationship categories: One-to-One, One-to-Many, Many-to-Many, Many-to-Many with custom pivot attributes, and Has-Many-Through.

---

## Milestone Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Database Design & Mermaid ERD | ✅ Done | 2026-10-02 | [`docs/database/erd.md`](../database/erd.md) |
| J2 | Migrations (9 Tables + 2 Pivot Tables) | ✅ Done | 2026-10-02 | [`database/migrations`](../../database/migrations) |
| J3 | Eloquent Models & Relationships | ✅ Done | 2026-10-02 | [`app/Models`](../../app/Models) |
| J4 | Model Factories & Seeders | ✅ Done | 2026-10-02 | [`database/factories`](../../database/factories), [`database/seeders`](../../database/seeders) |
| J5 | Documentation & Verification | ✅ Done | 2026-10-02 | [`README.md`](../../README.md) |

---

### J1: Database Design and ERD
- **Status:** ✅ Done
- **Summary:** Engineered a relational architecture supporting MSME food ordering and catering workflows. Encompasses 9 entity tables and 2 pivot tables with clear relationship demarcations.
- **Proof:** [`docs/database/erd.md`](../database/erd.md)

### J2: Migrations
- **Status:** ✅ Done
- **Summary:** Created 9 clean migration files with foreign key constraints, `cascadeOnDelete`, `restrictOnDelete`, and unique indexing.
- **Proof:** [`database/migrations`](../../database/migrations)
- **Verification:** Command `php artisan migrate:fresh --seed` executes with 0 errors.

### J3: Models and Relationships
- **Status:** ✅ Done
- **Summary:** Created Eloquent models: `User`, `CustomerProfile`, `Merchant`, `Category`, `Menu`, `Tag`, `Order`, `OrderItem`, `Review`. All relationships include appropriate casts and inverse definitions.
- **Proof:** [`app/Models`](../../app/Models)

### J4: Factories and Seeders
- **Status:** ✅ Done
- **Summary:** Created model factories with realistic Faker definitions, and implemented seeders with authentic Banjar regional culinary data (*Soto Banjar*, *Nasi Kuning Haruan*, *Wadai Bingka*, *Amparan Tatak*, *Es Limau Kuit*).
- **Proof:** [`database/seeders/DatabaseSeeder.php`](../../database/seeders/DatabaseSeeder.php)

### J5: Documentation and Verification
- **Status:** ✅ Done
- **Summary:** Comprehensive English documentation, local development setup instructions, and verified test passes (`php artisan test`).
