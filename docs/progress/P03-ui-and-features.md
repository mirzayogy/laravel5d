# P03: Redesign and Extra Features

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-11-01 |
| **Completed** | 2026-11-02 |
| **Branch** | `feature/p03-ui-and-features` |
| **Pull request** | [PRs by deadyiss](https://github.com/mirzayogy/laravel5d/pulls?q=is%3Apr+author%3Adeadyiss) |

## Goal
Give Flagboard a playful, minimal, and informative design in sky blue and coral, make every page useful, and add the features a real competition platform needs, with plenty of demo data.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Design system and layout | ✅ Done | 2026-11-02 | [components](https://github.com/deadyiss/laravel5d/tree/feature/p03-ui-and-features/resources/views/components), [layout](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/resources/views/layouts/app.blade.php) |
| J2 | Accounts: verification, password reset, profile, notifications | ✅ Done | 2026-11-02 | [AuthController](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Http/Controllers/AuthController.php), [PasswordResetController](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Http/Controllers/PasswordResetController.php) |
| J3 | Teams: captain, member limit, directory | ✅ Done | 2026-11-02 | [TeamController](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Http/Controllers/TeamController.php) |
| J4 | Competition phases, challenge board, scoreboard | ✅ Done | 2026-11-02 | [ContestService](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Services/ContestService.php), [Competition](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Models/Competition.php) |
| J5 | Writeups in Markdown and the admin area | ✅ Done | 2026-11-02 | [WriteupController](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Http/Controllers/WriteupController.php), [Admin controllers](https://github.com/deadyiss/laravel5d/tree/feature/p03-ui-and-features/app/Http/Controllers/Admin) |
| J6 | Migrations and demo data | ✅ Done | 2026-11-02 | [migrations](https://github.com/deadyiss/laravel5d/tree/feature/p03-ui-and-features/database/migrations), [DemoDataSeeder](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/database/seeders/DemoDataSeeder.php) |
| J7 | Automated tests | ✅ Done | 2026-11-02 | [tests/Feature](https://github.com/deadyiss/laravel5d/tree/feature/p03-ui-and-features/tests/Feature) |
| J8 | Documentation and progress report | ✅ Done | 2026-11-02 | [README.md](../../README.md), [erd.md](../database/erd.md) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Design system and layout
- **Status:** ✅ Done, 2026-11-02
- **What:** A sky blue and coral theme in `resources/css/app.css`, 20 Blade components (icons, buttons, cards, badges, form fields, stats, empty states, progress bars, countdown, page header), a responsive layout with a mobile menu, notification bell, toasts, and footer, a custom pagination view, and custom 403, 404, 419, 429, 500, and 503 pages. Alpine.js adds the small interactions and Chart.js draws the charts.
- **Proof:** [`resources/views/components`](https://github.com/deadyiss/laravel5d/tree/feature/p03-ui-and-features/resources/views/components), [`resources/js/app.js`](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/resources/js/app.js)

### J2: Accounts: verification, password reset, profile, notifications
- **Status:** ✅ Done, 2026-11-02
- **What:** Email verification (needed for registering teams, flags, hints, and writeups), password reset by email, a profile page with password change, and in-app notifications through Laravel's database channel.
- **Proof:** [`EmailVerificationController`](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Http/Controllers/EmailVerificationController.php), [`NotificationController`](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Http/Controllers/NotificationController.php)
- **Verified:** `AccountTest` and `NotificationTest` in `php artisan test`.

### J3: Teams: captain, member limit, directory
- **What:** Every team has a captain who can rename the team, renew the invite code, remove members, hand over the role, and disband the team (only without submissions). A team holds up to 4 players. A public directory and a public page for every team.
- **Proof:** [`TeamController`](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Http/Controllers/TeamController.php), migration `add_captain_id_to_teams_table`
- **Verified:** `TeamManagementTest` in `php artisan test`.

### J4: Competition phases, challenge board, scoreboard
- **What:** The phase of a competition (draft, upcoming, running, finished) follows its start and end time, with countdown timers. Challenges unlock at the start. The challenge board has search and filters, solve counts, and first blood badges. The scoreboard has a podium, a score-over-time chart, and refreshes itself while the competition is running. Dashboard, activity feed, and competition pages show rank, progress, and news.
- **Proof:** [`ContestService`](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Services/ContestService.php), [`ScoreboardController`](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Http/Controllers/ScoreboardController.php)
- **Verified:** `PhaseTest` and `ScoreboardDataTest` in `php artisan test`.

### J5: Writeups in Markdown and the admin area
- **What:** Writeups and challenge descriptions are rendered from Markdown with HTML stripped, with a live preview in the editor and search on the list. The admin area gets an overview with a chart, user and role management, team management, filters on every list, and category and tag pages.
- **Proof:** [`WriteupController`](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/app/Http/Controllers/WriteupController.php), [`Admin` controllers](https://github.com/deadyiss/laravel5d/tree/feature/p03-ui-and-features/app/Http/Controllers/Admin)
- **Verified:** `MarkdownTest` and `AdminManagementTest` in `php artisan test`.

### J6: Migrations and demo data
- **What:** Two migrations (`teams.captain_id` and the `notifications` table). `DemoDataSeeder` creates 14 teams, about 40 players, 5 competitions in every phase, 53 challenges with hints and tags, more than 700 submissions spread over time, writeups, and notifications. The flag of every challenge is `FLAG{demo_<challenge id>}`.
- **Proof:** [`database/migrations`](https://github.com/deadyiss/laravel5d/tree/feature/p03-ui-and-features/database/migrations), [`DemoDataSeeder`](https://github.com/deadyiss/laravel5d/blob/feature/p03-ui-and-features/database/seeders/DemoDataSeeder.php)
- **Verified:** `DemoDataTest` seeds the database and opens every main page.

### J7: Automated tests
- **What:** 89 feature tests, including tests that seed the demo data and open every main page as a guest, a player, and an admin.
- **Verified:** `php artisan test` passes (89 tests, 324 assertions).

### J8: Documentation and progress report
- **What:** README (features, rules, demo accounts, pages, structure, roadmap), the ERD in `docs/database/erd.md` and the README (captain and notifications), and this file.
- **Not done:** real mail delivery (the log driver is used), file attachments for challenges, and per-team challenge instances.

---

## Next
Later phases get their own file in this folder, each with its own jobs (J1, J2, ...).

Reference jobs in commit messages, for example `P03-J4: add competition phases and the live scoreboard`.
