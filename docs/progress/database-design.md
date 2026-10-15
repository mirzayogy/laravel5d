erDiagram
    %% ============================================
    %% ERD: Sistem Pengaduan Masyarakat/Mahasiswa
    %% Framework: Laravel
    %% ============================================

    %% NOTE: Tabel pengguna, role: admin / user (role-based access)
    users {
        bigint id PK
        varchar name
        varchar email UK
        timestamp email_verified_at "nullable"
        varchar password
        varchar identity_number UK "nullable, NIM/NIK"
        varchar phone "nullable"
        enum role "admin, user; default user"
        varchar remember_token "nullable"
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at "nullable, soft delete"
    }

    %% NOTE: Kategori pengaduan (fasilitas, akademik, dll)
    categories {
        bigint id PK
        varchar name UK
        varchar slug UK
        text description "nullable"
        timestamp created_at
        timestamp updated_at
    }

    %% NOTE: Tabel utama laporan. Status: diterima -> diproses -> selesai
    %% NOTE: Index pada user_id, category_id, status, ticket_code
    complaints {
        bigint id PK
        bigint user_id FK "pelapor, index"
        bigint category_id FK "index"
        varchar ticket_code UK "contoh ADU-20261006-0001"
        varchar title
        text description
        varchar location "nullable"
        date incident_date "nullable"
        enum status "diterima, diproses, selesai; default diterima; index"
        boolean is_anonymous "default false"
        bigint handled_by FK "nullable, admin penangan"
        timestamp completed_at "nullable"
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at "nullable, soft delete"
    }

    %% NOTE: Foto/bukti laporan, 1 laporan bisa banyak file (upload file)
    complaint_attachments {
        bigint id PK
        bigint complaint_id FK "cascade on delete"
        varchar file_path
        varchar file_name "nama asli file"
        varchar mime_type
        integer file_size "KB"
        timestamp created_at
        timestamp updated_at
    }

    %% NOTE: Tanggapan/komentar dari admin atau user pada laporan
    complaint_responses {
        bigint id PK
        bigint complaint_id FK "cascade on delete"
        bigint user_id FK "penulis tanggapan"
        text message
        timestamp created_at
        timestamp updated_at
    }

    %% NOTE: Log riwayat perubahan status (tidak punya updated_at)
    complaint_status_histories {
        bigint id PK
        bigint complaint_id FK "cascade on delete"
        bigint changed_by FK "admin pengubah status"
        enum old_status "nullable"
        enum new_status "diterima, diproses, selesai"
        text note "nullable"
        timestamp created_at
    }

    %% ============================================
    %% RELASI
    %% ============================================

    %% NOTE: 1 user bisa membuat banyak laporan
    users ||--o{ complaints : "membuat (user_id)"

    %% NOTE: 1 admin bisa menangani banyak laporan, handled_by boleh kosong
    users |o--o{ complaints : "menangani (handled_by)"

    %% NOTE: 1 kategori punya banyak laporan
    categories ||--o{ complaints : "mengelompokkan"

    %% NOTE: 1 laporan punya banyak lampiran
    complaints ||--o{ complaint_attachments : "memiliki"

    %% NOTE: 1 laporan punya banyak tanggapan
    complaints ||--o{ complaint_responses : "memiliki"

    %% NOTE: 1 user bisa menulis banyak tanggapan
    users ||--o{ complaint_responses : "menulis"

    %% NOTE: 1 laporan minimal punya 1 riwayat status (saat dibuat = diterima)
    complaints ||--|{ complaint_status_histories : "memiliki riwayat"

    %% NOTE: 1 admin bisa mengubah banyak status
    users ||--o{ complaint_status_histories : "mengubah status (changed_by)"