# Database Design: Entity Relationship Diagram

This document describes the database schema of **GameBook**, a gaming session booking application, and every Eloquent relationship used in the project.

## 1. Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o| PROFILES : "has one"
    USERS ||--o{ BOOKINGS : "makes"
    USERS ||--o{ REVIEWS : "writes"

    PLATFORMS ||--o{ STATIONS : "has"
    STATIONS ||--o{ BOOKINGS : "is booked in"
    STATIONS ||--o{ REVIEWS : "receives"
    STATIONS ||--o{ GAME_STATION : "installed via"
    GAMES ||--o{ GAME_STATION : "available via"

    BOOKINGS ||--o| PAYMENTS : "paid by"
    BOOKINGS ||--o{ ADDON_BOOKING : "includes"
    ADDONS ||--o{ ADDON_BOOKING : "added via"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
    }
    PROFILES {
        bigint id PK
        bigint user_id FK "unique"
        string nickname
        string gamer_tag
        string phone
    }
    PLATFORMS {
        bigint id PK
        string name
        string slug UK
        text description
    }
    STATIONS {
        bigint id PK
        bigint platform_id FK
        string name
        int price_per_hour
        boolean is_active
    }
    GAMES {
        bigint id PK
        string title UK
        string genre
    }
    GAME_STATION {
        bigint station_id FK
        bigint game_id FK
    }
    BOOKINGS {
        bigint id PK
        bigint user_id FK
        bigint station_id FK
        date booking_date
        time start_time
        time end_time
        string status
        int total_price
    }
    ADDONS {
        bigint id PK
        string name UK
        int price
    }
    ADDON_BOOKING {
        bigint booking_id FK
        bigint addon_id FK
        smallint quantity
        int unit_price
    }
    PAYMENTS {
        bigint id PK
        bigint booking_id FK "unique"
        string method
        int amount
        string status
        timestamp paid_at
    }
    REVIEWS {
        bigint id PK
        bigint user_id FK
        bigint station_id FK
        tinyint rating "1-5"
        text comment
    }
```

## 2. Relationship Summary

| Type                      | Relationship                                                        | Eloquent                      |
| ------------------------- | ------------------------------------------------------------------- | ----------------------------- |
| One-to-One                | `User` and `Profile`                                                | `hasOne` / `belongsTo`        |
| One-to-One                | `Booking` and `Payment`                                             | `hasOne` / `belongsTo`        |
| One-to-Many               | `Platform` to `Station`                                             | `hasMany` / `belongsTo`       |
| One-to-Many               | `Station` to `Booking`                                              | `hasMany` / `belongsTo`       |
| One-to-Many               | `User` to `Booking`                                                 | `hasMany` / `belongsTo`       |
| One-to-Many               | `User` to `Review`, `Station` to `Review`                           | `hasMany` / `belongsTo`       |
| Many-to-Many              | `Station` and `Game` (pivot `game_station`)                         | `belongsToMany`               |
| Many-to-Many + pivot data | `Booking` and `Addon` (pivot `addon_booking`: `quantity`, `unit_price`) | `belongsToMany` + `withPivot` |
| Has-Many-Through          | `Platform` to `Booking` through `Station`                           | `hasManyThrough`              |
| Has-Many-Through          | `User` to `Payment` through `Booking`                               | `hasManyThrough`              |

## 3. Design Notes

- **Unique constraints:** `profiles.user_id`, `payments.booking_id`, `platforms.slug`, `games.title`, `addons.name`, and (`reviews.user_id`, `reviews.station_id`) so a user reviews a station only once.
- **Cascade rules:** deleting a user removes their profile, bookings, payments, and reviews. Deleting a platform or a station is restricted while bookings or stations still use it.
- **Pivot tables:** `game_station` and `addon_booking` use a composite primary key. `addon_booking` also stores the `quantity` and the `unit_price` at the time of booking.
