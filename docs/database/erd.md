# Database Design — Anti-Procrastination App

This document contains the Entity Relationship (ER) and Entity Relationship Diagram (ERD) for the Anti-Procrastination App.

## 1. Entity Relationship (ER)

The ER below shows the main relationships between entities without displaying all attributes.

```mermaid
erDiagram
    USERS ||--o{ CATEGORIES : has
    USERS ||--o{ TASKS : creates
    USERS ||--o{ FOCUS_SESSIONS : has
    USERS ||--o{ NOTIFICATIONS : receives
    USERS ||--o{ USER_STATISTICS : has

    CATEGORIES ||--o{ TASKS : contains

    TASKS ||--o{ SUBTASKS : has
    TASKS ||--o{ TASK_DELAYS : has
    TASKS ||--o{ RESCUE_PLANS : has
    TASKS ||--o{ FOCUS_SESSIONS : has
    TASKS ||--o{ NOTIFICATIONS : has
```

## 2. Entity Relationship Diagram (ERD)

The ERD below shows the entities, attributes, primary keys, foreign keys, and relationships.

```mermaid
erDiagram

    USERS ||--o{ CATEGORIES : has
    USERS ||--o{ TASKS : creates
    USERS ||--o{ FOCUS_SESSIONS : has
    USERS ||--o{ NOTIFICATIONS : receives
    USERS ||--o{ USER_STATISTICS : has

    CATEGORIES ||--o{ TASKS : contains

    TASKS ||--o{ SUBTASKS : has
    TASKS ||--o{ TASK_DELAYS : has
    TASKS ||--o{ RESCUE_PLANS : has
    TASKS ||--o{ FOCUS_SESSIONS : has
    TASKS ||--o{ NOTIFICATIONS : has

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        bigint id PK
        bigint user_id FK
        string category_name
        timestamp created_at
        timestamp updated_at
    }

    TASKS {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        string title
        text description
        datetime deadline
        string difficulty
        int estimated_minutes
        decimal priority_score
        string status
        datetime completed_at
        timestamp created_at
        timestamp updated_at
    }

    SUBTASKS {
        bigint id PK
        bigint task_id FK
        string title
        string status
        int estimated_minutes
        int order_number
        datetime completed_at
        timestamp created_at
        timestamp updated_at
    }

    TASK_DELAYS {
        bigint id PK
        bigint task_id FK
        datetime old_deadline
        datetime new_deadline
        text reason
        timestamp delayed_at
        timestamp created_at
        timestamp updated_at
    }

    RESCUE_PLANS {
        bigint id PK
        bigint task_id FK
        int remaining_minutes
        int days_remaining
        int daily_target_minutes
        text recommendation
        timestamp created_at
        timestamp updated_at
    }

    FOCUS_SESSIONS {
        bigint id PK
        bigint user_id FK
        bigint task_id FK
        datetime start_time
        datetime end_time
        int duration_minutes
        string status
        timestamp created_at
        timestamp updated_at
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        bigint task_id FK
        text message
        datetime notification_time
        boolean is_read
        string type
        timestamp created_at
        timestamp updated_at
    }

    USER_STATISTICS {
        bigint id PK
        bigint user_id FK
        date date
        int completed_tasks
        int delayed_tasks
        int focus_minutes
        decimal productivity_score
        int current_streak
        timestamp created_at
        timestamp updated_at
    }
```

## 3. Relationship Summary

| Relationship | Type |
| --- | --- |
| User → Category | One-to-Many |
| User → Task | One-to-Many |
| Category → Task | One-to-Many |
| Task → Subtask | One-to-Many |
| Task → TaskDelay | One-to-Many |
| Task → RescuePlan | One-to-Many |
| User → FocusSession | One-to-Many |
| Task → FocusSession | One-to-Many |
| User → Notification | One-to-Many |
| Task → Notification | One-to-Many |
| User → UserStatistic | One-to-Many |

## 4. Design Notes

- `users.email` is unique.
- Each category belongs to one user.
- Each task belongs to one user.
- A task may belong to one category.
- Each subtask belongs to one task.
- Each task delay belongs to one task.
- Each rescue plan belongs to one task.
- Focus sessions belong to a user and may be associated with a task.
- Notifications belong to a user and may be associated with a task.
- Each user can have one statistic record per date.
