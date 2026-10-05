# P01 - Database Design and Table Relationships

| | |
|---|---|
| **Student** | M. David Almadani |
| **NPM** | (2410010487) |
| **Class** | TI 5C REG BJB |
| **Project** | Minisoccer (sports venue booking) |
| **Branch** | feature/database-relations |

## Job Status

| Job | Description | Status | Date | Evidence |
|---|---|---|---|---|
| J1 | Database design and ERD | Done | 2026-10-05 | [erd.md](https://github.com/davidalmadani07-wq/5C/blob/feature/database-relations/docs/database/erd.md) |
| J2 | Migrations (10 tables) | Done | 2026-10-05 | [migrations](https://github.com/davidalmadani07-wq/5C/tree/feature/database-relations/database/migrations) |
| J3 | Eloquent models and relationships | Done | 2026-10-05 | [Models](https://github.com/davidalmadani07-wq/5C/tree/feature/database-relations/app/Models) |
| J4 | Factories and seeders | Done | 2026-10-05 | [seeders](https://github.com/davidalmadani07-wq/5C/tree/feature/database-relations/database/seeders) |
| J5 | Progress report | Done | 2026-10-05 | this file |

## Relationships Implemented

| Type | Tables | Eloquent method |
|---|---|---|
| One-to-One | users and profiles | hasOne / belongsTo |
| One-to-Many | venues to fields, teams to players | hasMany / belongsTo |
| Many-to-Many | facilities and fields | belongsToMany |
| Many-to-Many + pivot | teams and tournaments | belongsToMany + withPivot |
| Has-Many-Through | venues to bookings via fields | hasManyThrough |

## How to Verify

```
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
```

## Notes

(kendala dan cara mengatasinya, misalnya migration yang awalnya salah kolom lalu diperbaiki)