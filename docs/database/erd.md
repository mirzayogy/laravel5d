# Database Design — Entity Relationship Diagram

Dokumen ini menjelaskan skema database aplikasi **pelacak menstruasi** (terinspirasi Flo) beserta seluruh relasi Eloquent yang dipakai.

## 1. Fitur

| Fitur | Keterangan | Tabel utama |
|-------|------------|-------------|
| Profil & tujuan | Rata-rata siklus/haid, tanggal lahir, tujuan (lacak siklus / program hamil) | `profiles` |
| Catat periode | Tanggal mulai-selesai haid dan intensitas aliran harian | `cycles`, `period_logs` |
| Gejala | Kram, sakit kepala, jerawat, kembung, dll. dengan tingkat keparahan | `symptoms`, `symptom_logs` |
| Mood | Skala 1–5 per hari | `mood_entries` |
| Catatan harian | Jurnal teks, opsional terhubung ke mood | `journal_entries` |
| Log harian ala Flo | Suhu basal, cairan vagina, aktivitas seksual, tes ovulasi, tes kehamilan, berat badan, air minum, tidur | `daily_logs` |
| Obat & suplemen | Pil KB, pereda nyeri, zat besi + catatan konsumsi | `medications`, `medication_logs` |
| Pengingat | Haid segera datang, masa subur, minum pil, catat harian | `reminders` |
| Prediksi & fase siklus | Haid berikutnya, masa subur, ovulasi, fase siklus | dihitung dari `cycles` |
| Statistik & peringatan | Grafik siklus, gejala tersering, siklus tidak teratur | dihitung dari log |

## 2. Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o| PROFILES : "has one"
    USERS ||--o{ CYCLES : "has"
    CYCLES ||--o{ PERIOD_LOGS : "has"
    USERS ||--o{ PERIOD_LOGS : "records"

    USERS ||--o{ SYMPTOM_LOGS : "logs"
    SYMPTOMS ||--o{ SYMPTOM_LOGS : "logged as"

    USERS ||--o{ MOOD_ENTRIES : "records"
    USERS ||--o{ JOURNAL_ENTRIES : "writes"
    MOOD_ENTRIES ||--o| JOURNAL_ENTRIES : "may have"

    USERS ||--o{ DAILY_LOGS : "records"

    USERS ||--o{ MEDICATIONS : "takes"
    MEDICATIONS ||--o{ MEDICATION_LOGS : "has"
    MEDICATIONS ||--o{ REMINDERS : "may have"
    USERS ||--o{ REMINDERS : "sets"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
    }
    PROFILES {
        bigint id PK
        bigint user_id FK "unique"
        date birth_date
        string goal "track_cycle / get_pregnant"
        int avg_cycle_length "default 28"
        int avg_period_length "default 5"
        string avatar
        string timezone
    }
    CYCLES {
        bigint id PK
        bigint user_id FK
        date start_date
        date end_date "nullable"
        int cycle_length "nullable, diisi saat siklus berikutnya mulai"
        int period_length "nullable"
    }
    PERIOD_LOGS {
        bigint id PK
        bigint user_id FK
        bigint cycle_id FK
        date log_date
        string flow_level "spotting / light / medium / heavy"
    }
    SYMPTOMS {
        bigint id PK
        string name
        string slug UK
        string category "physical / skin / digestive / ..."
        string icon
    }
    SYMPTOM_LOGS {
        bigint id PK
        bigint user_id FK
        bigint symptom_id FK
        date log_date
        tinyint severity "1-3"
    }
    MOOD_ENTRIES {
        bigint id PK
        bigint user_id FK
        date entry_date
        tinyint mood_level "1-5"
        string note
    }
    JOURNAL_ENTRIES {
        bigint id PK
        bigint user_id FK
        bigint mood_entry_id FK "nullable"
        date entry_date
        string title
        text content
    }
    DAILY_LOGS {
        bigint id PK
        bigint user_id FK
        date log_date
        decimal basal_temp "nullable"
        string discharge "nullable"
        string sex_activity "nullable"
        string ovulation_test "nullable"
        string pregnancy_test "nullable"
        decimal weight "nullable"
        int water_ml "nullable"
        decimal sleep_hours "nullable"
    }
    MEDICATIONS {
        bigint id PK
        bigint user_id FK
        string name
        string type "pill / painkiller / supplement"
        string dosage
        boolean is_active
    }
    MEDICATION_LOGS {
        bigint id PK
        bigint medication_id FK
        date taken_date
        time taken_time "nullable"
    }
    REMINDERS {
        bigint id PK
        bigint user_id FK
        bigint medication_id FK "nullable"
        string type "period_soon / fertile / pill / daily_log"
        int days_before "nullable"
        time remind_at
        boolean is_enabled
    }
```

## 3. Relationship Summary

| Type | Relationship | Eloquent |
|------|--------------|----------|
| One-to-One | `User` ↔ `Profile` | `hasOne` / `belongsTo` |
| One-to-One (optional) | `MoodEntry` ↔ `JournalEntry` | `hasOne` / `belongsTo` |
| One-to-Many | `User` → `Cycle` | `hasMany` / `belongsTo` |
| One-to-Many | `Cycle` → `PeriodLog` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `PeriodLog` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `MoodEntry` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `JournalEntry` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `DailyLog` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Medication` | `hasMany` / `belongsTo` |
| One-to-Many | `Medication` → `MedicationLog` | `hasMany` / `belongsTo` |
| One-to-Many | `User` → `Reminder` | `hasMany` / `belongsTo` |
| One-to-Many | `Medication` → `Reminder` (opsional) | `hasMany` / `belongsTo` |
| Many-to-Many + pivot data | `User` ↔ `Symptom` (pivot `symptom_logs`, kolom `log_date`, `severity`) | `belongsToMany` + `withPivot` |
| Has-Many-Through | `User` → `PeriodLog` through `Cycle` | `hasManyThrough` |

## 4. Symptoms (seeded)

Tabel `symptoms` diisi seeder dengan gejala bawaan, misalnya: Kram perut, Sakit kepala, Nyeri payudara, Kembung, Jerawat, Nyeri punggung, Mual, Lelah, Craving makanan, Insomnia.

## 5. Design Notes

- **Unique constraints:** `profiles.user_id`, `symptoms.slug`, (`period_logs.user_id`, `period_logs.log_date`), (`mood_entries.user_id`, `mood_entries.entry_date`), (`daily_logs.user_id`, `daily_logs.log_date`), dan (`symptom_logs.user_id`, `symptom_logs.symptom_id`, `symptom_logs.log_date`).
- **Journal ↔ mood:** `journal_entries.mood_entry_id` nullable dan unique, jadi jurnal bisa ada tanpa mood, tetapi satu mood paling banyak punya satu jurnal.
- **Cycle:** `cycle_length` dan `period_length` diisi otomatis saat siklus berikutnya dimulai (selisih `start_date`).
- **Prediksi tidak disimpan:** haid berikutnya = `start_date` terakhir + rata-rata panjang 3–6 siklus terakhir; ovulasi ≈ 14 hari sebelum haid berikutnya; masa subur = 5 hari sebelum sampai 1 hari setelah ovulasi. Fase siklus (menstruasi, folikular, ovulasi, luteal) juga dihitung, bukan disimpan.
- **Peringatan:** siklus < 21 atau > 35 hari ditandai tidak teratur saat statistik dihitung.
- **Cascade rules:** menghapus user menghapus seluruh data miliknya (profil, siklus, log, mood, jurnal, obat, pengingat). Menghapus `medications` menghapus `medication_logs`-nya, sedangkan `reminders.medication_id` di-set null.
