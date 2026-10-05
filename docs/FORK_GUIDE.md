# Panduan Fork dan Pull Request

Panduan ini menjelaskan cara mengerjakan tugas di repository dosen lewat fork, lalu mengirimkannya sebagai Pull Request.

1. **Fork** repository dosen (`mirzayogy/laravel5c`) ke akun GitHub sendiri lewat tombol **Fork**.
2. **Clone** hasil fork:
   ```powershell
   git clone https://github.com/febrainrot/laravel5c.git
   cd laravel5c
   ```
3. **Buat branch kerja:**
   ```powershell
   git checkout -b feature/database-relations
   ```
4. **Siapkan proyek:**
   ```powershell
   composer install
   copy .env.example .env
   php artisan key:generate
   ```
5. **Kerjakan tugas**, lalu commit dan push:
   ```powershell
   git add .
   git commit -m "feat: add migrations, models, factories, and seeders"
   git push -u origin feature/database-relations
   ```
6. **Buka Pull Request** dari branch `feature/database-relations` di fork ke `mirzayogy:main`.
7. **Judul PR:**
   ```
   [Assignment 1] Table Relationships: PartsHub - Ahmad Zainal Febryan - 2410010414 - TI 5C REG BJB
   ```
8. Isi deskripsi PR dengan tabel identitas, status J1 sampai J5, dan bukti pengerjaan.

## Menyinkronkan Fork dengan Repository Dosen

```powershell
git remote add upstream https://github.com/mirzayogy/laravel5c.git
git fetch upstream
git merge upstream/main
```
