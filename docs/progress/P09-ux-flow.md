# P09: End-to-End Flow Improvements

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-28 |
| **Completed** | 2026-10-28 |
| **Branch** | `feature/habitude` |

## Goal
Remove friction from the whole journey, from signing up to closing the day, in three waves: quick wins, connecting the flow, and the larger features.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Wave 1: open menu, undo, time-ordered list, quick add, no dead ends | ✅ Done | 2026-10-28 | [`UxFlowTest`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/tests/Feature/UxFlowTest.php) |
| J2 | Wave 2: mood to journal prompt, celebration, missed-day nudge, password reset | ✅ Done | 2026-10-28 | [`PasswordResetController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/PasswordResetController.php), [`celebrate.js`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/js/ui/celebrate.js) |
| J3 | Wave 3a: reminder delivery (email and browser) | ✅ Done | 2026-10-28 | [`SendReminders`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Console/Commands/SendReminders.php), [`notifier.js`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/js/ui/notifier.js) |
| J4 | Wave 3b: installable app (PWA) | ✅ Done | 2026-10-28 | [`manifest`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/public/manifest.webmanifest), [`sw.js`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/public/sw.js) |
| J5 | Wave 3c: command palette | ✅ Done | 2026-10-28 | [`palette.js`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/js/ui/palette.js) |
| J6 | Wave 3d: Indonesian language | ✅ Done | 2026-10-28 | [`lang/id.json`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/lang/id.json), [`LocaleTest`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/tests/Feature/LocaleTest.php) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Wave 1
- **Menu:** Track and Master Data start open, so Habits, Mood and Journal are one click away.
- **Undo:** habits are soft deleted for 30 days (`model:prune` removes them after that); the toast after deleting has **Undo**, and so does unchecking a habit. Every query that joins `habits` skips deleted ones, and a category that only has deleted habits still cannot be removed.
- **Today's list:** ordered by the reminder time that applies today, under Morning / Afternoon / Evening / Anytime headers, with the time on each row.
- **Quick add:** a name and a category are enough; the target and unit come from the category. The redirect target is checked so it can never leave the site.
- **No dead ends:** the magic link and SSO buttons were removed (they did nothing), and Terms and Privacy are real pages.

### J2: Wave 2
- **Mood to journal:** saving a mood offers "Write about it", opening the journal with a question that fits how the day felt (low, mid or high).
- **Celebration:** confetti and a toast when every habit of today is done (skipped with reduced motion).
- **Nudge:** a banner offers to log yesterday when nothing was checked and a habit already existed; "Not now" hides it for the session.
- **Password reset:** "Forgot password?", a throttled request page that answers the same whether or not the account exists, and a single-use token link (60 minutes).

### J3: Wave 3a, reminders that reach you
- `php artisan habitude:send-reminders` runs every minute through the scheduler. It works per timezone, sends one email per person listing every habit due, only once a day per reminder, skips habits already done, switched off, paused or deleted, and catches up reminders missed in the last four minutes.
- A profile switch turns emails off. Browser notifications use a small JSON feed (`/reminders/today`) and fire while the app is open or installed.
- **Verified end to end:** a reminder due now produced a real email through the mailer ("Time for Walk"), the second run sent nothing, and a due reminder showed a browser notification in Chrome.
- **Not included:** background push when the app is closed (needs a push service and keys). Emails cover that case.

### J4: Wave 3b, installable app
- Web app manifest, icons (192, 512, maskable, Apple), a service worker that never caches pages (only hashed build assets and icons), an offline page, and an install button in the profile and palette. Verified in Chrome: the worker activates and a request without network shows the offline page.

### J5: Wave 3c, command palette
- Cmd/Ctrl + K opens a search over pages, actions (add a habit, log a mood, write in the journal, toggle the sidebar, install, log out), the admin pages for admins, and the person's own habits. Keyboard driven; never lists anyone else's data.

### J6: Wave 3d, Indonesian
- The interface can be English or Bahasa Indonesia. The choice is stored in the profile (and in the session on the sign-in pages, where the browser's language is the starting point). English text is the translation key in `lang/id.json` (about 590 entries), plus Indonesian validation, auth and pagination messages.
- Covered: every page, flash and validation message, the reminder email, the greeting, journal prompts, mood and unit names, the seeded category and achievement names, dates (day and month names), and the JavaScript (toasts, skyline, palette, notifications).
- **Guards:** a test fails if any `__('…')` or `t('…')` phrase in the code has no Indonesian entry, one checks the config lists, and one scans pages in both languages for leaked template code.
- **Bugs found while doing it:** a quote inside a Blade attribute leaked `$category->name])"` onto the admin pages in English too, and a variable named `t` inside the skyline code shadowed the translator and crashed the dashboard (found by crawling the pages in a real browser).
- **Left in English on purpose:** text people typed or that was seeded as data (habit names, journal text, tags).

## Notes
- Production needs one cron line for the scheduler, a real `APP_URL`, and SMTP settings; see the README.
- 126 tests pass.
