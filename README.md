# Recipe App - Academic Project

A Laravel-based web application designed to manage culinary recipes, categories, ingredients, user profiles, and reviews. Built for the Object-Oriented Programming II (PBO2) coursework.

## Features & Architecture

* **User Management & Profiles**: One-to-One relationship (`users` & `profiles`).
* **Recipe Catalog**: Categorized recipes supporting preparation time, cooking time, and difficulty levels (`categories` & `recipes`).
* **Ingredients & Pivots**: Many-to-Many relationship between recipes and ingredients with pivot quantities (`recipe_ingredient`).
* **Interactive Reviews & Favorites**:
  * One-to-Many reviews for recipes (`reviews`).
  * Many-to-Many favorite bookmarks per user (`favorites`).
  * Has-Many-Through relation for gathering reviews through user recipes.

  ## Database Schema (ERD Summary)

  The application utilizes 8 core database tables:
1. `users`
2. `profiles`
3. `categories`
4. `recipes`
5. `ingredients`
6. `reviews`
7. `favorites` (pivot)
8. `recipe_ingredient` (pivot)

Detailed Mermaid ERD documentation is available at [`docs/database/erd.md`](docs/database/erd.md).

## Local Development Setup

### Prerequisites
* PHP >= 8.2
* Composer
* MySQL / MariaDB
* Node.js & NPM

## Installation Steps

### 1. **Clone the repository:**
   git clone [https://github.com/AishaNazela/PBO2_LARAVEL_5C.git](https://github.com/AishaNazela/PBO2_LARAVEL_5C.git)
   cd PBO2_LARAVEL_5C
   
###  2. **Install PHP dependencies:**
    composer install

### 3. **Configure Environment:**
    cp .env.example .env
    php artisan key:generate

### 4. **Run Migrations and Seeders:**
    php artisan migrate:fresh --seed

### 5. **Start the Application:**
    php artisan serve       

## License

This project is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).