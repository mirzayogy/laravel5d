# Mealmate — Recipe Collection & Weekly Meal Planner

Assignment 1: Database Design & Relationships (Laravel, Eloquent).

| | |
|---|---|
| Student | M. Ridhoni Akbar |
| NPM | 2410010367 |
| Class | TI 5C REG BJB |
| Phase | P01: Database Design |

## About

Mealmate lets users store recipes (with steps and ingredients), review and favorite recipes, and plan their meals per day in a weekly meal plan.

The full schema and every relationship are documented in [erd.md](erd.md).

## What was done

| Job | Description | Status |
|---|---|---|
| J1 | Database design and ERD with Mermaid | Done |
| J2 | Migrations: 8 tables plus 2 pivot tables | Done |
| J3 | Eloquent models and relationships | Done |
| J4 | Factories and seeders | Done |
| J5 | README and fork guide | Done |

Relationship types covered: One-to-One, One-to-Many, Many-to-Many, Many-to-Many with pivot data (`quantity`/`unit`, `planned_date`/`meal_type`/`servings`), and Has-Many-Through.

## Tables

`users`, `profiles`, `categories`, `recipes`, `recipe_steps`, `ingredients`, `reviews`, `meal_plans`, plus pivot tables `recipe_ingredient`, `favorites`, and `meal_plan_items`.

## Relationships

| Type | Relationship | Eloquent |
|---|---|---|
| One-to-One | `User` ↔ `Profile` | `hasOne` / `belongsTo` |
| One-to-Many | `User` → `Recipe`, `Review`, `MealPlan` | `hasMany` / `belongsTo` |
| One-to-Many | `Category` → `Recipe` | `hasMany` / `belongsTo` |
| One-to-Many | `Recipe` → `RecipeStep`, `Review` | `hasMany` / `belongsTo` |
| Many-to-Many + pivot data | `Recipe` ↔ `Ingredient` (`quantity`, `unit`) | `belongsToMany` + `withPivot` |
| Many-to-Many + pivot data | `MealPlan` ↔ `Recipe` (`planned_date`, `meal_type`, `servings`) | `belongsToMany` + `withPivot` |
| Many-to-Many | `User` ↔ `Recipe` as favorites | `belongsToMany` |
| Has-Many-Through | `User` → `Review` through `Recipe` | `hasManyThrough` |
| Has-Many-Through | `Category` → `Review` through `Recipe` | `hasManyThrough` |

## Requirements

- PHP 8.2 or newer
- Composer
- MySQL (or another database supported by Laravel)

## Fork guide (setup from scratch)

1. Fork this repository on GitHub, then clone your fork:

   ```
   git clone https://github.com/<your-username>/laravel5c.git
   cd laravel5c
   ```

2. Install dependencies and create the environment file:

   ```
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. Create an empty database (for example `mealmate`) and set it in `.env`:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mealmate
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Run the migrations and seeders:

   ```
   php artisan migrate --seed
   ```

   To rebuild everything from scratch: `php artisan migrate:fresh --seed`.

5. Start the app:

   ```
   php artisan serve
   ```

## Verify the relationships

Open `php artisan tinker` and try:

```php
$recipe = App\Models\Recipe::first();
$recipe->ingredients;                 // many-to-many with pivot (quantity, unit)
$recipe->ingredients->first()->pivot->quantity;
$recipe->steps;                       // one-to-many, ordered by step_number

$user = App\Models\User::first();
$user->profile;                       // one-to-one
$user->favoriteRecipes;               // many-to-many
$user->receivedReviews;               // has-many-through (User -> Review via Recipe)

App\Models\Category::first()->reviews;   // has-many-through (Category -> Review via Recipe)
App\Models\MealPlan::first()->recipes;   // many-to-many with pivot (planned_date, meal_type, servings)
```

## Seeded data

`php artisan migrate --seed` creates 8 categories, 20 ingredients, 5 users (including `test@example.com`, password `password`) with profiles, 15 recipes with steps, ingredients, and reviews, plus favorites and one meal plan per user.
