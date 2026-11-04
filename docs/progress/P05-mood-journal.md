# P05: Mood and Journal

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-28 |
| **Completed** | 2026-10-28 |
| **Branch** | `feature/habitude` |
| **Module** | [Laravel 5: CRUD Periode dengan Template AdminLTE](https://mirzayogy.github.io/blog/2026-10-13-laravel-simtikum-5/) |

## Goal
Daily mood check-in and journal entries that link to the day's mood.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Mood check-in with Peek Rating and history | ✅ Done | 2026-10-28 | [`MoodEntryController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/MoodEntryController.php), [`views/mood`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/mood) |
| J2 | Journal CRUD with list and grid views | ✅ Done | 2026-10-28 | [`JournalEntryController`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/app/Http/Controllers/JournalEntryController.php), [`views/journal`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/resources/views/journal) |
| J3 | One-to-one link between mood and journal | ✅ Done | 2026-10-28 | [`TrackingTest`](https://github.com/dzakwannajmi/habitude/tree/feature/habitude/tests/Feature/TrackingTest.php) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Mood
- One mood per user per day: saving again updates it. Rated with five hearts and a label tooltip (Awful to Great). The dashboard has the same rating for quick saving.

### J2: Journal
- Entries are grouped by month, shown as a list of swipe rows or a Finder-style grid.

### J3: One-to-one link
- A journal links to the mood of its date only when that mood has no journal yet. A mood saved later picks up that day's unlinked journal. Deleting a mood keeps the journal (foreign key `nullOnDelete`).

## Next
P06: reminders and achievements.
