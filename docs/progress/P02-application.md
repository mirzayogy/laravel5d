# P02: Application Features

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-28 |
| **Completed** | 2026-10-28 |
| **Branch** | `feature/p02-application` |
| **Pull request** | [PRs by deadyiss](https://github.com/mirzayogy/laravel5d/pulls?q=is%3Apr+author%3Adeadyiss) |

## Goal
Build the Flagboard web application on top of the P01 database: authentication with roles, teams, competition registration, challenges with flag submission, hints, the scoreboard, writeups, and an admin area, with feature tests.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Authentication and role-based access | ✅ Done | 2026-10-28 | [AuthController](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/AuthController.php), [EnsureUserHasRole](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Middleware/EnsureUserHasRole.php) |
| J2 | Team management and competition registration | ✅ Done | 2026-10-28 | [TeamController](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/TeamController.php), [CompetitionController](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/CompetitionController.php) |
| J3 | Challenge management and flag submission | ✅ Done | 2026-10-28 | [Admin controllers](https://github.com/deadyiss/laravel5d/tree/feature/p02-application/app/Http/Controllers/Admin), [SubmissionController](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/SubmissionController.php) |
| J4 | Hints and scoreboard | ✅ Done | 2026-10-28 | [HintUnlockController](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/HintUnlockController.php), [ContestService](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Services/ContestService.php) |
| J5 | Writeups | ✅ Done | 2026-10-28 | [WriteupController](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/WriteupController.php), [views](https://github.com/deadyiss/laravel5d/tree/feature/p02-application/resources/views/writeups) |
| J6 | Automated tests | ✅ Done | 2026-10-28 | [tests/Feature](https://github.com/deadyiss/laravel5d/tree/feature/p02-application/tests/Feature) |
| J7 | Documentation and progress report | ✅ Done | 2026-10-28 | [README.md](../../README.md), [branch commits](https://github.com/deadyiss/laravel5d/commits/feature/p02-application) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Authentication and role-based access
- **Status:** ✅ Done, 2026-10-28
- **What:** Register, log in, and log out with Laravel's session guard. Registration creates a `player` user and an empty profile. The `EnsureUserHasRole` middleware limits the `/admin` pages to the `admin` role (403 for others, redirect to login for guests).
- **Proof:** [`AuthController`](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/AuthController.php), [`EnsureUserHasRole`](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Middleware/EnsureUserHasRole.php), [`routes/web.php`](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/routes/web.php)
- **Verified:** `AuthTest` in `php artisan test`.

### J2: Team management and competition registration
- **Status:** ✅ Done, 2026-10-28
- **What:** A player creates a team (a random 8-character invite code is generated), joins a team with an invite code, or leaves a team. A team registers for a competition only while it is `running`; draft competitions are hidden.
- **Proof:** [`TeamController`](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/TeamController.php), [`CompetitionController`](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/CompetitionController.php)
- **Verified:** `TeamTest` and `CompetitionRegistrationTest` in `php artisan test`.

### J3: Challenge management and flag submission
- **Status:** ✅ Done, 2026-10-28
- **What:** Admins create, edit, and delete competitions and challenges. A flag is entered in plain text and stored only as a hash (`Hash::make`); leaving it empty on edit keeps the old hash. Tags can be chosen or created from a comma-separated field. Registered teams see the published challenges and submit flags (`Hash::check`). Every attempt is stored in `submissions`, a team can solve a challenge only once, and submissions are accepted only while the competition is `running`.
- **Proof:** [`Admin` controllers](https://github.com/deadyiss/laravel5d/tree/feature/p02-application/app/Http/Controllers/Admin), [`ChallengeController`](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/ChallengeController.php), [`SubmissionController`](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/SubmissionController.php)
- **Verified:** `FlagSubmissionTest` and `AdminTest` in `php artisan test`.

### J4: Hints and scoreboard
- **Status:** ✅ Done, 2026-10-28
- **What:** A team unlocks a hint once (stored in `hint_unlocks`), and not after solving the challenge. The scoreboard ranks the registered teams by score (points of solved challenges minus the cost of unlocked hints); ties are broken by the earlier last solve, then by team name. The ranking logic is in `ContestService`.
- **Proof:** [`HintUnlockController`](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/HintUnlockController.php), [`ScoreboardController`](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/ScoreboardController.php), [`ContestService`](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Services/ContestService.php)
- **Verified:** `HintAndScoreboardTest` in `php artisan test`.

### J5: Writeups
- **Status:** ✅ Done, 2026-10-28
- **What:** A player writes a writeup for a challenge only after the player's team solved it. Writeups are `draft` (visible to the author only) or `published` (visible to everyone), and only the author can edit or delete.
- **Proof:** [`WriteupController`](https://github.com/deadyiss/laravel5d/blob/feature/p02-application/app/Http/Controllers/WriteupController.php), [`resources/views/writeups`](https://github.com/deadyiss/laravel5d/tree/feature/p02-application/resources/views/writeups)
- **Verified:** `WriteupTest` in `php artisan test`.

### J6: Automated tests
- **Status:** ✅ Done, 2026-10-28
- **What:** Seven feature test classes with a shared `FlagboardTestCase` (refreshes the database and disables Vite). `ExampleTest` from the course skeleton now uses `RefreshDatabase`, because the home page reads the database.
- **Proof:** [`tests/Feature`](https://github.com/deadyiss/laravel5d/tree/feature/p02-application/tests/Feature)
- **Verified:** `php artisan test` passes (44 tests, 132 assertions).

### J7: Documentation and progress report
- **Status:** ✅ Done, 2026-10-28
- **What:** README updated with business rules, demo accounts, pages, how to run the tests, project structure, and a completed roadmap. This file records phase P02.
- **Proof:** [`README.md`](../../README.md), [`docs/progress/P02-application.md`](P02-application.md), [branch commits](https://github.com/deadyiss/laravel5d/commits/feature/p02-application)
- **Not done:** password reset, email verification, file attachments for challenges, and per-team challenge instances.

---

## Next
Later phases get their own file in this folder, for example `P03-...md`, each with its own jobs (J1, J2, ...).

Reference jobs in commit messages, for example `P02-J3: add challenge management and flag submission`.
