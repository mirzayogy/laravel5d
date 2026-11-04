# P11: Release Preparation

| | |
|---|---|
| **Status** | 🚧 In progress |
| **Started** | 2026-10-29 |
| **Completed** | - |
| **Branch** | `feature/habitude` |

## Goal
Get the project ready to hand in and show: license, credits, mascot, and deployment or demo.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | MIT license file | ✅ Done | 2026-10-29 | [`LICENSE`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/LICENSE) |
| J2 | Check the React Bits license and record third-party credits | ✅ Done | 2026-10-29 | [`THIRD_PARTY_NOTICES.md`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/THIRD_PARTY_NOTICES.md) |
| J3 | Mood mascot (bear) | ⏳ Planned | - | artwork to be generated, then wired into the mood page |
| J4 | Deployment or recorded demo | ⏳ Planned | - | - |
| J5 | Pre-push audit and rename to `habitude` | ✅ Done | 2026-10-30 | see J5 below |
| J6 | Production safety: seeders and branch name | ✅ Done | 2026-10-30 | [`SeederSafetyTest`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/tests/Feature/SeederSafetyTest.php) |
| J7 | Sync with the lecturer's `main` and open the pull request | ✅ Done | 2026-11-04 | merge commit on `feature/habitude` |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: MIT license
- Added the standard MIT `LICENSE` (2026, Muhammad Dzakwan Najmi); `composer.json` already said MIT.

### J2: React Bits
- React Bits is licensed under **MIT + Commons Clause** (David Haz). Using its components inside an app or product, commercial or not, is allowed; selling, sublicensing or redistributing the components themselves is not.
- Habitude is a coursework app and does not sell the components, so using the ports here is fine. The ported files are listed in `THIRD_PARTY_NOTICES.md` and stay under the original terms; the repository's MIT license does not widen them.
- Checked from the project's license file and its introduction page on 2026-10-29. The text was read through a summary, so read `LICENSE.md` in the React Bits repository once yourself before publishing publicly.
- The logo, design tokens and Contribution Skyline were supplied by the author and have no recorded source; noted for confirmation.

### J5: Pre-push audit and rename
- **Audit:** the 234 tracked files hold no `.env`, database, log, key or build output (all ignored). No tokens or keys in any commit, and the real database password never appeared in the history. No personal data beyond the author line in the README. `CLAUDE.md` and `AGENTS.md` come from the course template.
- **Rename:** the repository is now `dzakwannajmi/habitude` (it stays a fork of `mirzayogy/laravel5d`, and PR #1 keeps working because GitHub redirects the old name). Links in the README, progress docs, the sidebar and the feedback URL use the new name; `package.json` gets `"name": "habitude"`. The local folder is renamed to `habitude`.

### J6: Production safety
- `DatabaseSeeder` (demo accounts with a well-known password) refuses to run in production; there, run `CategorySeeder`, `AchievementSeeder` and `AdminSeeder` one by one.
- `AdminSeeder` refuses to create a new administrator in production while `ADMIN_PASSWORD` is empty or `password`. An existing account is only promoted and keeps its password.
- The working branch is now `feature/habitude` (was `feature/crud-ui`), and the links in these progress documents follow it.
- Removed the "Laravel MCP" line from the README tech stack, since the package is not used by the app.

### J7: Sync with the lecturer's repository
- Merged `upstream/main` (`mirzayogy/laravel5d`, 3 new commits) into `feature/habitude`, so the pull request has no conflicts. `feature/database-relations` was already contained in this branch.
- New from the lecturer: the `Periode` model, `PeriodeController`, the `periode` table migration and `PeriodeSeeder` (the course module's example). They are kept as they are; `DatabaseSeeder` also calls `PeriodeSeeder`. The habit tracker does not use them.
- Two conflicts were resolved in favor of Habitude: `.env.example` keeps the MySQL defaults (`DB_DATABASE=habitude`), and `DatabaseSeeder` keeps the demo data.
- Existing databases need `php artisan migrate` to get the `periode` table.
