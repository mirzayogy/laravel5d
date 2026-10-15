# Database Design — Entity Relationship Diagram

This document describes the database schema of **Personal Finance Tracker** and every Eloquent relationship used in the project.

## 1. Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o{ ACCOUNTS : "owns"
    USERS ||--o{ CATEGORIES : "creates (custom)"
    USERS ||--o{ TRANSACTIONS : "makes"
    USERS ||--o{ BUDGETS : "sets"
    USERS ||--o{ FINANCIAL_GOALS : "plans"

    ACCOUNTS ||--o{ TRANSACTIONS : "has"
    CATEGORIES ||--o{ TRANSACTIONS : "groups"
    CATEGORIES ||--o{ BUDGETS : "tracks"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
    }
    ACCOUNTS {
        bigint id PK
        bigint user_id FK
        string name "e.g., Cash, BCA, GoPay"
        decimal balance
        string type "cash, bank, e-wallet"
    }
    CATEGORIES {
        bigint id PK
        bigint user_id FK "nullable (null = system default)"
        string name
        string type "income, expense"
        string icon
    }
    TRANSACTIONS {
        bigint id PK
        bigint user_id FK
        bigint account_id FK
        bigint category_id FK
        decimal amount
        date transaction_date
        string type "income, expense"
        text description
    }
    BUDGETS {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        decimal amount
        string month_year "MM-YYYY"
    }
    FINANCIAL_GOALS {
        bigint id PK
        bigint user_id FK
        string name
        decimal target_amount
        decimal current_amount
        date target_date
    }
```

## 2. Relationship Summary

| Type | Relationship | Eloquent |
|---|---|---|
| One-to-Many | `User` → `Account` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Transaction` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Category` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Budget` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `FinancialGoal` | `hasMany` / `belongsTo` |
| One-to-Many | `Account` → `Transaction` | `hasMany` / `belongsTo` |
| One-to-Many | `Category` → `Transaction` | `hasMany` / `belongsTo` |
| One-to-Many | `Category` → `Budget` | `hasOne` / `belongsTo` |
| Has-Many-Through | `User` → `Transaction` through `Account` | `hasManyThrough` |

## 3. Categories (seeded)

The `categories` table is populated by a seeder with the default built-in themes (user_id = null):

| Name | Type | Icon |
|---|---|---|
| Salary | `income` | 💰 |
| Freelance | `income` | 💻 |
| Food & Dining | `expense` | 🍔 |
| Transportation | `expense` | 🚗 |
| Utilities | `expense` | ⚡ |
| Entertainment | `expense` | 🎬 |
| Shopping | `expense` | 🛍️ |
| Health | `expense` | 🏥 |

## 4. Design Notes

- **Category System:** The `categories.user_id` is nullable. If it is `null`, it means the category is a global default provided by the system. If it has a user ID, it is a custom category created by that specific user.
- **Account Balances:** Balances in the `ACCOUNTS` table should be updated automatically whenever a `TRANSACTION` is created, updated, or deleted.
- **Cascade rules:** Deleting a `User` cascades to their accounts, transactions, budgets, goals, and custom categories. Deleting an `Account` will cascade delete its related transactions. Deleting a `Category` is restricted while transactions still use it.