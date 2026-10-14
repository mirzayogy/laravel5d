# ERD Basis Data Laundry

Diagram relasi antar tabel. GitHub, GitLab, dan VS Code (dengan ekstensi Markdown Preview Mermaid Support) menampilkan blok `mermaid` di bawah sebagai gambar.

```mermaid
erDiagram
    PELANGGAN ||--o{ TRANSAKSI : memesan
    KARYAWAN ||--o{ TRANSAKSI : melayani
    TRANSAKSI ||--|{ DETAIL_TRANSAKSI : memiliki
    LAYANAN ||--o{ DETAIL_TRANSAKSI : dipilih
    TRANSAKSI ||--o{ PEMBAYARAN : dibayar

    PELANGGAN {
        int id_pelanggan PK
        varchar nama
        varchar no_hp
        text alamat
    }

    KARYAWAN {
        int id_karyawan PK
        varchar nama
        varchar username
        varchar password
        varchar role
    }

    LAYANAN {
        int id_layanan PK
        varchar nama_layanan
        decimal harga_per_kg
        int estimasi_hari
    }

    TRANSAKSI {
        int id_transaksi PK
        int id_pelanggan FK
        int id_karyawan FK
        date tgl_masuk
        date tgl_selesai
        varchar status
        decimal total_harga
    }

    DETAIL_TRANSAKSI {
        int id_detail PK
        int id_transaksi FK
        int id_layanan FK
        decimal berat_kg
        decimal subtotal
    }

    PEMBAYARAN {
        int id_pembayaran PK
        int id_transaksi FK
        date tgl_bayar
        varchar metode
        decimal jumlah_bayar
    }
```

## Keterangan Relasi

| Relasi | Kardinalitas | Arti |
|--------|--------------|------|
| `pelanggan` ke `transaksi` | 1 : N | Satu pelanggan bisa memesan berkali-kali |
| `karyawan` ke `transaksi` | 1 : N | Satu karyawan melayani banyak transaksi |
| `transaksi` ke `detail_transaksi` | 1 : N | Satu nota berisi satu atau lebih item cucian |
| `layanan` ke `detail_transaksi` | 1 : N | Satu layanan bisa muncul di banyak detail |
| `transaksi` ke `pembayaran` | 1 : N | Satu nota bisa dibayar sekali atau dicicil |

## Keterangan Simbol

- `PK`: primary key
- `FK`: foreign key
- `||--o{`: satu ke nol atau banyak
- `||--|{`: satu ke satu atau banyak
