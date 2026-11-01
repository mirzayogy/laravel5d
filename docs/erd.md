# ERD — PenyewaanCamp

```mermaid
erDiagram
    USERS ||--o{ PENYEWAANS : "membuat"
    KATEGORIS ||--o{ BARANGS : "memiliki"
    PENYEWAANS ||--|{ DETAIL_PENYEWAANS : "terdiri dari"
    BARANGS ||--o{ DETAIL_PENYEWAANS : "disewa dalam"
    PENYEWAANS ||--o| PEMBAYARANS : "dibayar lewat"

    USERS {
        bigint id PK
        string name
        string email
    }
    KATEGORIS {
        bigint id PK
        string nama_kategori UK
        text deskripsi
    }
    BARANGS {
        bigint id PK
        bigint kategori_id FK
        string nama_barang
        decimal harga_sewa_per_hari
        int stok
        enum kondisi
    }
    PENYEWAANS {
        bigint id PK
        bigint user_id FK
        date tanggal_sewa
        date tanggal_kembali
        date tanggal_dikembalikan
        decimal total_harga
        enum status
    }
    DETAIL_PENYEWAANS {
        bigint id PK
        bigint penyewaan_id FK
        bigint barang_id FK
        int jumlah
        decimal harga_satuan
        decimal subtotal
    }
    PEMBAYARANS {
        bigint id PK
        bigint penyewaan_id FK,UK
        date tanggal_bayar
        decimal jumlah_bayar
        enum metode
        enum status
    }
```
