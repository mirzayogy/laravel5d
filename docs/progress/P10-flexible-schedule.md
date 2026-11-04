# P10: Flexible Schedule and History

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-29 |
| **Completed** | 2026-10-29 |
| **Branch** | `feature/habitude` |

## Goal
Make streaks fair to real life. A habit like "work out 3 times a week" should not break because of one day off, and a missed day should be forgivable. Also keep the mood history short by default.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Weekly goals ("3 days a week") for habits | ✅ Done | 2026-10-29 | [`StreakService`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Services/StreakService.php), [`FlexibleScheduleTest`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/tests/Feature/FlexibleScheduleTest.php) |
| J2 | Rest days: one per week that keep the streak safe | ✅ Done | 2026-10-29 | [`RestDayController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/RestDayController.php), [`rest_days` migration](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/database/migrations/2026_10_28_220048_create_rest_days_table.php) |
| J3 | Mood history shows 5 rows, extendable by choice | ✅ Done | 2026-10-29 | [`history-list.js`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/js/ui/history-list.js) |
| J4 | Fix: check-ins looked like failed saves | ✅ Done | 2026-10-29 | [`tracker.js`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/js/ui/tracker.js), [`LocaleTest`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/tests/Feature/LocaleTest.php) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Weekly goals
- New column `habits.weekly_goal` (1 to 6, null means every day, so every existing habit is unchanged). Set it with **How often** in the habit form.
- A weekly habit's streak counts **weeks** (Monday to Sunday) that reached the goal. A week that is still open never breaks it. The streak badge shows `wk` instead of days.
- The dashboard row shows progress ("2 of 3 days this week") and updates after each check-in.
- The month calendar does not expect a weekly habit on every day: it only counts on the days it was logged.

### J2: Rest days
- New table `rest_days` (`user_id`, `rest_date`, unique per user and date; deleted with the user): `User` has many `RestDay`.
- A rest day neither counts toward nor breaks the current streak, the best streak, or a daily habit's streak.
- Rules: one per week (Monday to Sunday), only for today or yesterday, and not on a day that already has a completed habit. "Take a rest day" sits in the streak card, with Undo; the "log yesterday" nudge offers "It was a rest day", and disappears once yesterday is a rest day.

### J3: Mood history
- Shows 5 rows with "Showing 5 of N" and a **Show more** button; a select offers 5, 10, 20 or all rows and the choice is remembered in the browser. Up to 365 days are loaded.
- Built as a reusable script (`data-history` markup), the same idea as the dashboard's list.

### J4: Check-in bug
- **What happened:** in `tracker.js` a local variable named `t` (for the "total" counter) hid the translation function `t()`. Since Bahasa Indonesia was added, every check-in threw "Cannot access 't' before initialization" while updating the page, so it showed "Could not save" and reverted the checkbox, even though the server had saved it (a refresh showed the right state).
- **Why it was missed:** the Indonesian work only added tests for translation keys. An earlier browser run showed the odd counters and I wrongly put them down to test timing.
- **Fix:** the variable is renamed, and `LocaleTest` now fails if any script that imports `t` declares its own `t`.

## Verification
- `php artisan test`: 153 tests pass (27 new).
- Browser run on MySQL: rest day taken and undone, weekly row goes from "3 of 4" to "4 of 4", the mood history shows 5, extends, remembers a choice of 10 after reload and shows all; no console errors.
