# ERD MasakApa

Entity Relationship Diagram untuk database aplikasi MasakApa (P01: Database Design).

```mermaid
erDiagram
    USERS ||--o{ RECIPES : "menulis"
    CATEGORIES ||--o{ RECIPES : "mengelompokkan"
    RECIPES ||--o{ INGREDIENT_RECIPE : "memiliki"
    INGREDIENTS ||--o{ INGREDIENT_RECIPE : "dipakai di"
    USERS ||--o{ RECIPE_USER : "memfavoritkan"
    RECIPES ||--o{ RECIPE_USER : "difavoritkan"

    USERS {
        bigint id PK
        string name
        string email
        string password
    }
    CATEGORIES {
        bigint id PK
        string name
        string slug
    }
    INGREDIENTS {
        bigint id PK
        string name
        string unit
    }
    RECIPES {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        string title
        string slug
        text description
        text instructions
        smallint cooking_time
        tinyint servings
        enum difficulty
    }
    INGREDIENT_RECIPE {
        bigint id PK
        bigint recipe_id FK
        bigint ingredient_id FK
        decimal quantity
    }
    RECIPE_USER {
        bigint id PK
        bigint user_id FK
        bigint recipe_id FK
    }
```

## Keterangan Relasi

| Relasi | Jenis | Foreign key / pivot |
|---|---|---|
| `users` ke `recipes` | One-to-many | `recipes.user_id` |
| `categories` ke `recipes` | One-to-many | `recipes.category_id` |
| `recipes` dan `ingredients` | Many-to-many | pivot `ingredient_recipe` (kolom tambahan: `quantity`) |
| `users` dan `recipes` (favorit) | Many-to-many | pivot `recipe_user` |
