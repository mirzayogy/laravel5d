# P04: Dashboard, Calendar, and Streaks

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-28 |
| **Completed** | 2026-10-28 |
| **Branch** | `feature/habitude` |
| **Module** | [Laravel 5: CRUD Periode dengan Template AdminLTE](https://mirzayogy.github.io/blog/2026-10-13-laravel-simtikum-5/) |

## Goal
A home page where the day is tracked: check-ins, timers, a calendar, and streaks.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Daily check-in (spring check, +1, timer) | ✅ Done | 2026-10-28 | [`HabitLogController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/HabitLogController.php), [`tracker.js`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/js/ui/tracker.js) |
| J2 | Streak engine (per habit and overall) | ✅ Done | 2026-10-28 | [`StreakService`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Services/StreakService.php) |
| J3 | Month calendar with completion heat | ✅ Done | 2026-10-28 | [`CalendarService`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Services/CalendarService.php) |
| J4 | Dashboard stats and category progress | ✅ Done | 2026-10-28 | [`DashboardController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/DashboardController.php), [`dashboard.blade.php`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/dashboard.blade.php) |
| J5 | Year skyline: 2D heat map and 3D, one colour | ✅ Done | 2026-10-28 | [`skyline.js`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/js/ui/skyline.js) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Daily check-in
- **What:** One JSON endpoint (`mode` = `toggle`, `add`, or `set`) updates the day's log, and the page updates in place. Habits measured in minutes get a timer whose digits roll like an odometer. Past days can be logged, future days are rejected.
- **Verified:** `TrackingTest` and the browser: check, uncheck, +1, timer start/stop.

### J2: Streaks
- A day counts for a habit when the logged value reaches the target. The overall streak counts days with at least one completed habit; a still-open today does not break it. Best streak is computed from the same dates.

### J3: Calendar
- Monday-first month grid. Each day is shaded by completed / total habits (ink shades only), shows today and the selected day, and links to that day. Back-filled days count even for habits created later.

### J4: Stats and category progress
- Four animated stat tiles. **By category** uses the Has-Many-Through relation `Category → Habit → HabitLog`, limited to the user and month.
- **Bug found by tests:** a hand-edited `?date=garbage` caused a 500; invalid dates now fall back to today.

### J5: Year skyline
- A port of the *Contribution Skyline* component ([`skyline.js`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/js/ui/skyline.js)): a GitHub-style heat map of the last year that folds into a 3D isometric skyline with one camera and a rising wave of bars. Toggle 2D/3D (remembered), drag to orbit, hover for the day, arrow keys, legend isolation, and a click opens that day on the dashboard.
- One number per day: how many habits reached their target. The stats (year total, busiest day, longest and current streak) use the same rule as the streak service.
- **One colour:** blocks are Ember at 28%, 50%, 75% and 100% opacity, so less activity is simply fainter. The month calendar uses the same single hue.
- **Rule for orange:** it means *your progress* (streak and stat numbers, completed check, progress bars, mood hearts, unlocked achievements, sign-up bars) next to the one primary button per screen. Black stays for structure and text, gray for the inactive. Small text is never orange (about 3.4:1 on white, enough for 24px+ numbers and icons only).

### J6: Greeting
- `App\Support\Greeting` writes a different welcome on every visit, such as "Good evening, Ayu. Ready to hit the gym?". The question comes from the category of a habit that is still open today (gym, read, water, self-care, ...), or from the streak ("Keep your 19-day streak alive?") or progress ("1 of 13 done"). Bedtime habits are only suggested in the evening; when everything is done it celebrates, and with no habits it invites you to add one. The opener follows the user's local time. Past days keep the plain date header.
- The profile picture (animated waves avatar) sits next to the greeting.
- **Verified:** 8 tests with a seeded random engine (name, line per category, finished habits not repeated, evening-only, time of day, variety).

## Next
P05: mood and journal.
