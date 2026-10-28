# P02: Application Features

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-27 |
| **Completed** | 2026-10-27 |
| **Branch** | `feature/p02-application` |
| **Pull request** | [PRs by PabloExelen](https://github.com/mirzayogy/laravel5d/pulls?q=is%3Apr+author%3APabloExelen) |

## Goal
Build the CafeOrder web application on top of the P01 database: authentication with roles, menu management, ordering, order status, order history, and feature tests.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Authentication and role-based access | ✅ Done | 2026-10-27 | [AuthController](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Controllers/AuthController.php), [EnsureUserHasRole](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Middleware/EnsureUserHasRole.php) |
| J2 | Menu, category, and tag management | ✅ Done | 2026-10-27 | [Admin controllers](https://github.com/PabloExelen/laravel5c/tree/feature/p02-application/app/Http/Controllers/Admin) |
| J3 | Menu browsing and ordering | ✅ Done | 2026-10-27 | [MenuController](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Controllers/MenuController.php), [OrderService](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Services/OrderService.php) |
| J4 | Order status and order history | ✅ Done | 2026-10-27 | [OrderController](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Controllers/OrderController.php), [Staff/OrderController](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Controllers/Staff/OrderController.php) |
| J5 | Automated tests | ✅ Done | 2026-10-27 | [tests/Feature](https://github.com/PabloExelen/laravel5c/tree/feature/p02-application/tests/Feature) |
| J6 | Documentation and progress report | ✅ Done | 2026-10-27 | [README.md](../../README.md), [branch commits](https://github.com/PabloExelen/laravel5c/commits/feature/p02-application) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Authentication and role-based access
- **Status:** ✅ Done, 2026-10-27
- **What:** Register, log in, and log out with Laravel's session guard. Registration creates a `customer` user and an empty profile. A profile page edits the name, phone, and address. The `EnsureUserHasRole` middleware limits the staff pages to `cashier` and `admin`, and the admin pages to `admin` (403 for other roles, redirect to login for guests).
- **Proof:** [`AuthController`](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Controllers/AuthController.php), [`ProfileController`](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Controllers/ProfileController.php), [`EnsureUserHasRole`](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Middleware/EnsureUserHasRole.php), [`routes/web.php`](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/routes/web.php)
- **Verified:** `AuthTest` and `ProfileTest` in `php artisan test`.

### J2: Menu, category, and tag management
- **Status:** ✅ Done, 2026-10-27
- **What:** Admins create, edit, and delete menus (with category, price, availability, and tags), categories, and tags. A category with menus and a menu used in orders cannot be deleted; a menu is marked unavailable instead.
- **Proof:** [`Admin` controllers](https://github.com/PabloExelen/laravel5c/tree/feature/p02-application/app/Http/Controllers/Admin), [`resources/views/admin`](https://github.com/PabloExelen/laravel5c/tree/feature/p02-application/resources/views/admin)
- **Verified:** `AdminTest` in `php artisan test`.

### J3: Menu browsing and ordering
- **Status:** ✅ Done, 2026-10-27
- **What:** The home page lists menus grouped by category with a tag filter. A customer enters a quantity for each menu and places one order. `OrderService` rejects an empty order, unknown or unavailable menus, copies the menu price to `order_items.unit_price`, and creates the order and its items in one transaction.
- **Proof:** [`MenuController`](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Controllers/MenuController.php), [`OrderController`](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Controllers/OrderController.php), [`OrderService`](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Services/OrderService.php)
- **Verified:** `MenuBrowsingTest` and `OrderPlacementTest` in `php artisan test`.

### J4: Order status and order history
- **Status:** ✅ Done, 2026-10-27
- **What:** Cashiers and admins see an order queue with a status filter and move an order `pending` → `preparing` → `completed` (or `cancelled`); other changes are rejected. A customer sees only their own orders, with the total of each order, and can cancel an order that is still `pending`.
- **Proof:** [`OrderController`](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Controllers/OrderController.php), [`Staff/OrderController`](https://github.com/PabloExelen/laravel5c/blob/feature/p02-application/app/Http/Controllers/Staff/OrderController.php), [`resources/views/orders`](https://github.com/PabloExelen/laravel5c/tree/feature/p02-application/resources/views/orders)
- **Verified:** `OrderStatusTest` in `php artisan test`.

### J5: Automated tests
- **Status:** ✅ Done, 2026-10-27
- **What:** Six feature test classes with a shared `CafeTestCase` (refreshes the database and disables Vite). `ExampleTest` from the course skeleton now uses `RefreshDatabase`, because the home page reads the database.
- **Proof:** [`tests/Feature`](https://github.com/PabloExelen/laravel5c/tree/feature/p02-application/tests/Feature)
- **Verified:** `php artisan test` passes (46 tests, 147 assertions).

### J6: Documentation and progress report
- **Status:** ✅ Done, 2026-10-27
- **What:** README updated with business rules, demo accounts, pages, how to run the tests, project structure, and a completed roadmap. This file records phase P02.
- **Proof:** [`README.md`](../../README.md), [`docs/progress/P02-application.md`](P02-application.md), [branch commits](https://github.com/PabloExelen/laravel5c/commits/feature/p02-application)
- **Not done:** password reset, email verification, online payment, and menu photos.

---

## Next
Later phases get their own file in this folder, for example `P03-...md`, each with its own jobs (J1, J2, ...).

Reference jobs in commit messages, for example `P02-J3: add menu browsing and ordering`.
