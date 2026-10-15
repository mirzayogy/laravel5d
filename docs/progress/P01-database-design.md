# P01 - Database Design: MySphere

**Nama Aplikasi:** MySphere  
**Deskripsi:** Aplikasi manajemen keuangan pribadi all-in-one yang mencakup pencatatan keuangan, investasi, utang, kebiasaan (habits), dan jurnal pribadi.

---

## 1. Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o{ ACCOUNTS : "owns"
    USERS ||--o{ CATEGORIES : "creates"
    USERS ||--o{ TRANSACTIONS : "makes"
    USERS ||--o{ BUDGETS : "sets"
    USERS ||--o{ FINANCIAL_GOALS : "plans"
    USERS ||--o{ ASSETS : "invests"
    USERS ||--o{ DEBTS : "owes"
    USERS ||--o{ HABITS : "tracks"
    USERS ||--o{ JOURNALS : "writes"
    USERS ||--o{ RECURRING_TRANSACTIONS : "schedules"

    ACCOUNTS ||--o{ TRANSACTIONS : "used_in"
    ACCOUNTS ||--o{ DEBT_PAYMENTS : "paid_from"
    ACCOUNTS ||--o{ RECURRING_TRANSACTIONS : "charged_to"

    CATEGORIES ||--o{ TRANSACTIONS : "categorizes"
    CATEGORIES ||--o{ BUDGETS : "budgeted_for"
    CATEGORIES ||--o{ RECURRING_TRANSACTIONS : "categorizes"

    ASSETS ||--o{ ASSET_TRANSACTIONS : "has_history"
    DEBTS ||--o{ DEBT_PAYMENTS : "has_payments"
    HABITS ||--o{ HABIT_LOGS : "has_logs"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
    }

    ACCOUNTS {
        bigint id PK
        bigint user_id FK
        string name
        decimal balance
        string type
    }

    CATEGORIES {
        bigint id PK
        bigint user_id FK
        string name
        string type
    }

    TRANSACTIONS {
        bigint id PK
        bigint user_id FK
        bigint account_id FK
        bigint category_id FK
        decimal amount
        date transaction_date
        string type
        text description
    }

    BUDGETS {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        decimal amount
        string month
    }

    FINANCIAL_GOALS {
        bigint id PK
        bigint user_id FK
        string name
        decimal target_amount
        decimal current_amount
        date target_date
    }

    ASSETS {
        bigint id PK
        bigint user_id FK
        string name
        string type
        decimal quantity
        decimal buy_price
        decimal current_price
    }

    ASSET_TRANSACTIONS {
        bigint id PK
        bigint asset_id FK
        string type
        decimal quantity
        decimal price
        date transaction_date
    }

    DEBTS {
        bigint id PK
        bigint user_id FK
        string contact_name
        string type
        decimal total_amount
        decimal remaining_amount
        date due_date
        string status
    }

    DEBT_PAYMENTS {
        bigint id PK
        bigint debt_id FK
        bigint account_id FK
        decimal amount
        date payment_date
    }

    HABITS {
        bigint id PK
        bigint user_id FK
        string name
        string frequency
        int goal_streak
    }

    HABIT_LOGS {
        bigint id PK
        bigint habit_id FK
        date date
        boolean completed
    }

    JOURNALS {
        bigint id PK
        bigint user_id FK
        string title
        text content
        string mood
        date date
    }

    RECURRING_TRANSACTIONS {
        bigint id PK
        bigint user_id FK
        bigint account_id FK
        bigint category_id FK
        string name
        string type
        decimal amount
        string frequency
        date next_due_date
    }
```

---

## 2. Relationship Summary

| Jenis | Relasi | Eloquent |
| --- | --- | --- |
| One-to-Many | `User` → `Account` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Transaction` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Category` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Budget` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `FinancialGoal` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Asset` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Debt` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Habit` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Journal` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `RecurringTransaction` | `hasMany` / `belongsTo` |
| One-to-Many | `Account` → `Transaction` | `hasMany` / `belongsTo` |
| One-to-Many | `Account` → `DebtPayment` | `hasMany` / `belongsTo` |
| One-to-Many | `Account` → `RecurringTransaction` | `hasMany` / `belongsTo` |
| One-to-Many | `Category` → `Transaction` | `hasMany` / `belongsTo` |
| One-to-Many | `Category` → `Budget` | `hasMany` / `belongsTo` |
| One-to-Many | `Category` → `RecurringTransaction` | `hasMany` / `belongsTo` |
| One-to-Many | `Asset` → `AssetTransaction` | `hasMany` / `belongsTo` |
| One-to-Many | `Debt` → `DebtPayment` | `hasMany` / `belongsTo` |
| One-to-Many | `Habit` → `HabitLog` | `hasMany` / `belongsTo` |

---

## 3. Category (Seeded)

Berikut adalah contoh data kategori awal yang akan di-*seed* ke dalam database untuk memudahkan pengguna dalam mencatat transaksi.

| No | Nama Kategori | Tipe | Deskripsi |
|---|---|---|---|
| 1 | Makanan & Minuman | expense | Pengeluaran untuk makan dan minum |
| 2 | Transportasi | expense | Biaya transportasi harian |
| 3 | Belanja | expense | Pembelian barang kebutuhan |
| 4 | Hiburan | expense | Pengeluaran untuk hiburan |
| 5 | Tagihan & Utilitas | expense | Listrik, air, internet, dll |
| 6 | Kesehatan | expense | Biaya kesehatan dan obat |
| 7 | Pendidikan | expense | Biaya pendidikan dan kursus |
| 8 | Gaji | income | Pemasukan dari gaji |
| 9 | Bonus | income | Pemasukan dari bonus |
| 10 | Investasi | income | Pemasukan dari hasil investasi |

---

## 4. Design Notes

- **Modularitas:** Tabel dikelompokkan menjadi 4 pilar utama: Finance, Wealth, Habits, dan Journaling.
- **Skalabilitas:** Setiap tabel terhubung ke `users`, sehingga aplikasi mendukung multi-user.
- **Fleksibilitas:** Kolom `type` pada `categories`, `transactions`, dan `assets` memungkinkan berbagai jenis data tanpa perlu membuat tabel baru.
- **Perhitungan:** Fitur seperti *Capital Loss*, *Drawdown*, dan *Budget Variance* dihitung di level aplikasi, bukan disimpan sebagai tabel terpisah.
- **Normalisasi:** Tabel `asset_transactions` tidak menyimpan `user_id` secara langsung, melainkan terhubung melalui `asset_id` untuk menghindari redundansi data.