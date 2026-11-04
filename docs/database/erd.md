# Database Design — Entity Relationship Diagram

This document describes the database schema of **Habitude** and every Eloquent relationship used in the project.

## 1. Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o| PROFILES : "has one"
    USERS ||--o{ HABITS : "owns"
    USERS ||--o{ REST_DAYS : "takes"
    USERS ||--o{ MOOD_ENTRIES : "records"
    USERS ||--o{ JOURNAL_ENTRIES : "writes"
    USERS ||--o{ ACHIEVEMENT_USER : "earns"
    ACHIEVEMENTS ||--o{ ACHIEVEMENT_USER : "awarded via"

    CATEGORIES ||--o{ HABITS : "groups"
    HABITS ||--o{ HABIT_LOGS : "has"
    HABITS ||--o{ REMINDERS : "has"
    HABITS ||--o{ HABIT_TAG : "tagged via"
    TAGS ||--o{ HABIT_TAG : "labels"

    MOOD_ENTRIES ||--o| JOURNAL_ENTRIES : "may have"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role "user or admin"
    }
    PROFILES {
        bigint id PK
        bigint user_id FK "unique"
        text bio
        string avatar
        string timezone
        int daily_goal
        timestamp onboarded_at "nullable"
    }
    CATEGORIES {
        bigint id PK
        string name
        string slug UK
        string icon
        string color
    }
    HABITS {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        string name
        text description
        int target_count
        string unit
        tinyint weekly_goal "nullable, 1 to 6, null means every day"
        boolean is_active
    }
    REST_DAYS {
        bigint id PK
        bigint user_id FK
        date rest_date "unique with user_id"
    }
    HABIT_LOGS {
        bigint id PK
        bigint habit_id FK
        date logged_date
        int value
        text note
    }
    TAGS {
        bigint id PK
        string name UK
    }
    HABIT_TAG {
        bigint habit_id FK
        bigint tag_id FK
    }
    REMINDERS {
        bigint id PK
        bigint habit_id FK
        time remind_at
        json days_of_week
        boolean is_enabled
    }
    MOOD_ENTRIES {
        bigint id PK
        bigint user_id FK
        date entry_date
        tinyint mood_level "1-5"
        string note
    }
    JOURNAL_ENTRIES {
        bigint id PK
        bigint user_id FK
        bigint mood_entry_id FK "nullable"
        date entry_date
        string title
        text content
    }
    ACHIEVEMENTS {
        bigint id PK
        string name
        text description
        string criteria
    }
    ACHIEVEMENT_USER {
        bigint user_id FK
        bigint achievement_id FK
        timestamp earned_at
    }
```

## 2. Relationship Summary

| Type | Relationship | Eloquent |
|---|---|---|
| One-to-One | `User` ↔ `Profile` | `hasOne` / `belongsTo` |
| One-to-One (optional) | `MoodEntry` ↔ `JournalEntry` | `hasOne` / `belongsTo` |
| One-to-Many | `User` → `Habit` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `RestDay` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `MoodEntry` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `JournalEntry` | `hasMany` / `belongsTo` |
| One-to-Many | `Category` → `Habit` | `hasMany` / `belongsTo` |
| One-to-Many | `Habit` → `HabitLog` | `hasMany` / `belongsTo` |
| One-to-Many | `Habit` → `Reminder` | `hasMany` / `belongsTo` |
| Many-to-Many | `Habit` ↔ `Tag` (pivot `habit_tag`) | `belongsToMany` |
| Many-to-Many + pivot data | `User` ↔ `Achievement` (pivot `achievement_user`, column `earned_at`) | `belongsToMany` + `withPivot` |
| Has-Many-Through | `User` → `HabitLog` through `Habit` | `hasManyThrough` |
| Has-Many-Through | `Category` → `HabitLog` through `Habit` | `hasManyThrough` |

## 3. Categories (seeded)

The `categories` table is populated by a seeder with the eight built-in themes:

| Name | Slug |
|---|---|
| Health & Fitness | `health-fitness` |
| Mindfulness | `mindfulness` |
| Productivity | `productivity` |
| Better Sleep | `better-sleep` |
| Stay Hydrated | `stay-hydrated` |
| Read More | `read-more` |
| Social Connections | `social-connections` |
| Self Care | `self-care` |

## 4. Design Notes

- **Unique constraints:** `profiles.user_id`, `categories.slug`, `tags.name`, and (`habit_logs.habit_id`, `habit_logs.logged_date`) so a habit has one log per day.
- **Daily mood:** (`mood_entries.user_id`, `mood_entries.entry_date`) is unique, giving one mood per user per day.
- **Journal ↔ mood link:** `journal_entries.mood_entry_id` is nullable and unique, so a journal entry can exist without a mood check-in, but a mood has at most one journal entry.
- **Cascade rules:** deleting a user cascades to their profile, habits, logs, reminders, moods, journals, and achievement links. Deleting a category is restricted while habits still use it.
- **Streaks** are computed from `habit_logs` and are not stored. A day counts for a habit when `habit_logs.value >= habits.target_count`; the overall streak counts days with at least one completed habit.
- **Weekly goals:** a habit with `weekly_goal` set (1 to 6) is a weekly habit. Its streak counts consecutive weeks (Monday to Sunday) with at least that many completed days, and an unfinished current week never breaks it.
- **Rest days:** a row in `rest_days` is a day the user chose to rest. It neither counts toward nor breaks a daily or overall streak. One per week, for today or yesterday, and not on a day that already has a completed habit (enforced in `RestDayController`).
- **Dates** in `habit_logs`, `mood_entries`, and `journal_entries` are stored as plain `Y-m-d` strings (`date:Y-m-d` cast), so equality queries and unique indexes behave the same on SQLite and MySQL.
- **Roles:** `users.role` is `user` or `admin`. It is not mass assignable; only the seeder, the admin console, and `habitude:make-admin` change it.
- **Onboarding:** `profiles.onboarded_at` is `NULL` until the user finishes the first-run stepper.
- **Achievement criteria** are stored as `type:threshold` (for example `streak:7`). Types: `logs`, `streak`, `moods`, `journals`, `habits`.
