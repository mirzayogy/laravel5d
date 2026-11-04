# P08: Admin Console and Beta Release Stage

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-28 |
| **Completed** | 2026-10-28 |
| **Branch** | `feature/habitude` |

## Goal
Split the app in two sides: an **Admin** console that manages the whole system, and the **Beta** app for users, clearly labelled as a preview and not production.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Roles: `users.role`, admin middleware, make-admin command | ✅ Done | 2026-10-28 | [`EnsureAdmin`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Middleware/EnsureAdmin.php), [`MakeAdmin`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Console/Commands/MakeAdmin.php) |
| J2 | Admin overview (system numbers, sign-ups, top categories) | ✅ Done | 2026-10-28 | [`OverviewController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/Admin/OverviewController.php) |
| J3 | User management (search, filter, edit, role, delete) | ✅ Done | 2026-10-28 | [`Admin/UserController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/Admin/UserController.php), [`views/admin`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/admin) |
| J4 | Master data moved behind the admin | ✅ Done | 2026-10-28 | [`routes/web.php`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/routes/web.php) |
| J5 | Beta stage label and preview banner | ✅ Done | 2026-10-28 | [`app-layout`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/components/app-layout.blade.php), [`logo`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/components/logo.blade.php) |
| J6 | Tests (59) and login-3 style sign-in page | ✅ Done | 2026-10-28 | [`AdminTest`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/tests/Feature/AdminTest.php) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Roles
- `users.role` is `user` (default) or `admin` (new migration). It is **not** mass assignable, so registration or the profile form cannot grant it (tested). Admins are made by the seeder, the admin console, or `php artisan habitude:make-admin email@example.com` (`--revoke` to undo).
- Every `/admin/*` route needs the `admin` middleware: guests go to login, regular users get **403**.

- **Admin seeder:** `php artisan db:seed --class=AdminSeeder` creates or promotes the admin without touching existing data (found when a database seeded before P08 had no admin account, so the login correctly said "credentials do not match").

### J2: Overview
- Six animated counters (users, habits, check-ins, moods, journals, unlocked achievements), sign-ups for the last 14 days, top categories across all users (Has-Many-Through `Category → Habit → HabitLog`), newest users, and system info (stage, environment, Laravel, PHP, database, timezone).

### J3: User management
- Search by name or email, filter by role, edit name, email, role and password, delete with a confirmation dialog. Deleting a user removes all of their data through the cascade rules; shared master data stays.
- Safety: an admin cannot change their own role or delete themselves, so the console can never be locked out.

### J4: Master data
- Categories, tags and achievements are shared by every user, so managing them is now admin-only (`/admin/categories`, `/admin/tags`, `/admin/achievements`). Regular users keep a read-only achievements gallery, and pick categories and tags in the habit form.

### J5: Two sides, two labels
- **User side:** logo badge **BETA** and a dark announcement bar "Beta preview, not production. Data may be reset." with a feedback link.
- **Admin side:** logo badge **ADMIN** and a bar "Admin console. Changes here affect every user." Admins also get an "Admin console" entry in their user menu.
- The label comes from `HABITUDE_STAGE` (`.env`, default `beta`). Set it to `production` and both the badge and the banner disappear from the user side.

### J6: Verification
- `php artisan test`: 59 tests pass (12 new in `AdminTest`). Browser run with Chrome on both sides, including the 403 page for a regular user.

## Next
Parked by the user for later: run on MySQL, reminder delivery, React Bits license.

## Sign-in pages
Login and register follow the login-3 layout: a rounded card, the logo and heading centred, rounded inputs, a primary button, an OR separator, an SSO button, and the terms line. Magic link, SSO and the legal pages are not built, so they are shown as in the design and answer with a "not available in the beta yet" toast.
