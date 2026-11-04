# P02: CRUD Pages and Interface

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-28 |
| **Completed** | 2026-10-28 |
| **Branch** | `feature/habitude` |
| **Module** | [Laravel 5: CRUD Periode dengan Template AdminLTE](https://mirzayogy.github.io/blog/2026-10-13-laravel-simtikum-5/) |

## Goal
Apply the module's CRUD pattern (resource controller, validation, success message, delete confirmation, active sidebar menu) to the Habitude tables, using the Brex-style design tokens, a shadcn sidebar-08 layout, and the Habitude logo.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Design tokens and sidebar layout | ✅ Done | 2026-10-28 | [`resources/css/app.css`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/css), [`app-layout`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/components) |
| J2 | Categories CRUD | ✅ Done | 2026-10-28 | [`CategoryController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers), [`views/categories`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/categories) |
| J3 | Tags CRUD | ✅ Done | 2026-10-28 | [`TagController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers), [`views/tags`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/tags) |
| J4 | Habits CRUD with category and tags | ✅ Done | 2026-10-28 | [`HabitController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers), [`views/habits`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/habits) |
| J5 | Feature tests | ✅ Done | 2026-10-28 | [`tests/Feature/CrudTest.php`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/tests/Feature) |

---

### J1: Design tokens and sidebar layout
- **Status:** ✅ Done, 2026-10-28
- **What:** Brex tokens (Ember, Paper, Fog, Mist, ...) and Inter in Tailwind `@theme`. Sidebar follows shadcn *sidebar-08* (inset variant): logo header, collapsible groups, repository link, user card, breadcrumb header, off-canvas on mobile. Logo is the star mark from `Habitude.svg` with the wordmark replaced by "Habitude".
- **Rules followed:** 12px radius on buttons, inputs and cards, 6px on tags; no shadows; Ember only on the primary action; Inter with negative tracking.
- **Note:** spacing tokens are plain CSS variables, not `@theme`, because `--spacing-8` etc. would redefine Tailwind's `p-8` and `m-16`.
- **Proof:** [`resources/css/app.css`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/css/app.css), [`components/app-layout.blade.php`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/components/app-layout.blade.php), [`components/logo.blade.php`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/components/logo.blade.php)

### J2: Categories CRUD
- **Status:** ✅ Done, 2026-10-28
- **What:** `Route::resource('categories')->except('show')`; index table with habit counts, create and edit forms with validation (unique name, hex color), generated slug, delete confirmation dialog. A category that still has habits cannot be deleted (the foreign key is restricted), and the user gets an explanation instead of an error page.
- **Proof:** [`CategoryController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/CategoryController.php), [`resources/views/categories`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/categories)

### J3: Tags CRUD
- **Status:** ✅ Done, 2026-10-28
- **What:** Same pattern as J2. Deleting a tag removes its `habit_tag` rows through the cascade rule.
- **Proof:** [`TagController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/TagController.php), [`resources/views/tags`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/tags)

### J4: Habits CRUD with category and tags
- **Status:** ✅ Done, 2026-10-28
- **What:** Index shows category, daily target, tags, and status using eager loading. The form has a category select, tag chips (`belongsToMany` with `sync`), and an active switch (read with `boolean()`, like the module's status checkbox). Habits are scoped to the current user.
- **Proof:** [`HabitController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/HabitController.php), [`resources/views/habits`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/habits)
- **Update:** the temporary `CurrentUser` stand-in was replaced by real authentication in [P03](P03-authentication-onboarding.md).

### J5: Feature tests
- **Status:** ✅ Done, 2026-10-28
- **What:** 14 tests, 71 assertions: pages render, validation (empty, duplicate, bad color), update without changing the unique field, delete blocked for categories with habits, tag detach on delete, tag sync on habit update, and another user's habit returns 404.
- **Verified:** `php artisan test` passes (14/14).
- **Proof:** [`tests/Feature/CrudTest.php`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/tests/Feature/CrudTest.php)

---

## Next
P03: authentication, then mood and journal CRUD, dashboard with streaks, reminders and achievements.
