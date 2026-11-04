# P06: Reminders and Achievements

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-28 |
| **Completed** | 2026-10-28 |
| **Branch** | `feature/habitude` |
| **Module** | [Laravel 5: CRUD Periode dengan Template AdminLTE](https://mirzayogy.github.io/blog/2026-10-13-laravel-simtikum-5/) |

## Goal
Per-habit reminders and badges that unlock automatically.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Reminders per habit (time and weekdays) | ✅ Done | 2026-10-28 | [`ReminderController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/ReminderController.php) |
| J2 | Achievement CRUD and gallery | ✅ Done | 2026-10-28 | [`AchievementController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/AchievementController.php), [`views/achievements`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/achievements) |
| J3 | Automatic unlocking with pivot `earned_at` | ✅ Done | 2026-10-28 | [`AchievementService`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Services/AchievementService.php) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Reminders
- Add, switch on or off, and delete reminders on the habit edit page. The dashboard lists the reminders of the selected weekday.
- **Not done:** reminders are shown in the app only. Sending them (push, email, or a scheduled command) is future work.

### J2: Achievements
- Admin-style CRUD. The goal is a type (`logs`, `streak`, `moods`, `journals`, `habits`) and a number, stored as `type:threshold`. Each card shows progress and the unlock date.

### J3: Unlocking
- After every check-in, mood, journal, or new habit the service attaches newly met achievements with `earned_at` (Many-to-Many with pivot data) and the page shows a toast. An achievement is earned once.

## Next
P07: UI kit and finishing.
