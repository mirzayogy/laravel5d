# P01: Database Design and Table Relationships

| Information | Detail |
| --- | --- |
| Status | ✅ Done |
| Started | 2026-10-04 |
| Completed | 2026-10-05 |
| Branch | `feature/database-relations` |
| Project | Anti-Procrastination App |

## Goal

Design and implement the database structure for the Anti-Procrastination App using Laravel migrations, Eloquent models, relationships, factories, and seeders.

## Jobs

| Code | Job | Status |
| --- | --- | --- |
| J1 | Database Design and ERD | ✅ Done |
| J2 | Database Migrations | ✅ Done |
| J3 | Models and Relationships | ✅ Done |
| J4 | Factories and Seeders | ✅ Done |
| J5 | Database Verification | ✅ Done |

## J1: Database Design and ERD

The Anti-Procrastination App database consists of nine main tables:

1. users
2. categories
3. tasks
4. subtasks
5. task_delays
6. rescue_plans
7. focus_sessions
8. notifications
9. user_statistics

The ERD documentation is available in:

```text
docs/database/erd.md
```

## J2: Database Migrations

Laravel migrations were created to define the database structure, columns, foreign keys, and relationships.

The main relationships are:

- User has many Categories.
- User has many Tasks.
- Category has many Tasks.
- Task has many Subtasks.
- Task has many Task Delays.
- Task has many Rescue Plans.
- Task has many Focus Sessions.
- User has many Focus Sessions.
- Task has many Notifications.
- User has many Notifications.
- User has many User Statistics.

Foreign keys are used to maintain data integrity between related tables.

## J3: Models and Relationships

Eloquent relationships were implemented using:

- `hasMany()`
- `belongsTo()`

The application contains the following models:

- User
- Category
- Task
- Subtask
- TaskDelay
- RescuePlan
- FocusSession
- Notification
- UserStatistic

Example relationships:

```php
public function tasks()
{
    return $this->hasMany(Task::class);
}
```

```php
public function user()
{
    return $this->belongsTo(User::class);
}
```

## J4: Factories and Seeders

Factories were created to generate sample data for testing.

The `DatabaseSeeder` generates:

- Users
- Categories
- Tasks
- Subtasks
- Task delay histories
- Rescue plans
- Focus sessions
- Notifications
- User statistics

The seeder also connects the sample data using the required foreign keys.

## J5: Database Verification

The database implementation was verified using the following commands:

```bash
php artisan migrate:fresh --seed
```

```bash
php artisan migrate:status
```

```bash
php artisan tinker
```

Relationships were tested using Laravel Tinker.

Example:

```php
$user = App\Models\User::first();
$user->categories;
$user->tasks;
```

```php
$task = App\Models\Task::first();
$task->category;
$task->subtasks;
$task->taskDelays;
$task->rescuePlans;
$task->focusSessions;
$task->notifications;
```

## Result

The database structure for the Anti-Procrastination App has been successfully implemented.

The project now includes:

- Database migrations
- Foreign key relationships
- Eloquent models
- Model relationships
- Factories
- Database seeders
- Sample data
- Database relationship testing

The database is ready to support the next development phase of the Anti-Procrastination App.
