# GameBook: Gaming Session Booking

A gaming session booking application built with **Laravel** for the course *Pemrograman Berorientasi Objek 2*. This repository is a fork of [mirzayogy/laravel5d](https://github.com/mirzayogy/laravel5d).

| | |
|---|---|
| **Student** | Muhammad Hersa Sugiannor |
| **NPM** | 2410010253 |
| **Class** | TI 5C REG BJB |
| **GitHub** | [@Hersa7](https://github.com/Hersa7) |

## Assignment 1: Table Relationships

The database models customers who book gaming stations (PC, PlayStation 5, Xbox, VR) by the hour, with optional add-ons and a payment per booking.

Relationship types covered: One-to-One, One-to-Many, Many-to-Many, Many-to-Many with pivot data (`quantity`, `unit_price`), and Has-Many-Through.

- ERD and relationship summary: [`docs/database/erd.md`](docs/database/erd.md)
- Progress report: [`docs/progress/P01-database-design.md`](docs/progress/P01-database-design.md)

## Getting Started

```
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Set `DB_CONNECTION=mysql` and your database name in `.env` before migrating.
