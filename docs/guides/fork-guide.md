# Panduan Fork Repo Dosen (Windows & macOS)

Panduan ini menjelaskan cara mengambil repo dosen, mengerjakan tugas di repo milikmu sendiri, dan tetap bisa mengambil pembaruan dari repo dosen.

**Istilah singkat**

| Istilah | Arti |
|---|---|
| **Fork** | Salinan repo dosen di akun GitHub-mu. Kamu bebas mengubahnya tanpa memengaruhi repo dosen. |
| **Clone** | Mengunduh repo fork-mu ke laptop. |
| **`origin`** | Repo fork milikmu (tempat kamu `push`). |
| **`upstream`** | Repo asli milik dosen (tempat kamu `pull` pembaruan). |
| **Branch** | Cabang kerja terpisah, supaya `main` tetap bersih. |

Repo dosen pada proyek ini: <https://github.com/mirzayogy/laravel5d>

---

## 1. Persiapan (sekali saja)

### Windows
1. Buat akun di <https://github.com>.
2. Pasang **Git for Windows**: <https://git-scm.com/download/win>. Pilih opsi bawaan saat instalasi.
3. Pasang PHP dan Composer lewat PowerShell (Run as Administrator):
   ```powershell
   Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
   ```
4. Pasang **Node.js LTS**: <https://nodejs.org>.
5. Tutup lalu buka ulang PowerShell, dan cek:
   ```powershell
   git --version
   php -v
   composer -V
   node -v
   ```

### macOS
1. Buat akun di <https://github.com>.
2. Buka **Terminal**. Pasang Homebrew jika belum ada: <https://brew.sh>.
3. Pasang semuanya:
   ```bash
   brew install git node
   /bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
   ```
4. Tutup lalu buka ulang Terminal, dan cek:
   ```bash
   git --version
   php -v
   composer -V
   node -v
   ```

### Atur identitas Git (Windows & macOS)
```bash
git config --global user.name "Nama Kamu"
git config --global user.email "email-github-kamu@example.com"
```

### Login GitHub dari terminal (disarankan: GitHub CLI)
- **Windows:** `winget install --id GitHub.cli`
- **macOS:** `brew install gh`

Lalu jalankan `gh auth login` dan ikuti petunjuknya (pilih GitHub.com, HTTPS, dan login lewat browser).

---

## 2. Fork repo dosen

### Lewat website (paling mudah)
1. Buka <https://github.com/mirzayogy/laravel5d> dan login.
2. Klik tombol **Fork** di kanan atas.
3. Pilih akun kamu sebagai **Owner**. Nama repo boleh dibiarkan atau diganti.
4. Klik **Create fork**.

Sekarang ada salinan di `https://github.com/USERNAME-KAMU/laravel5d`.

### Lewat terminal (opsional)
```bash
gh repo fork mirzayogy/laravel5d --clone
```
Perintah ini sekaligus mem-fork, meng-clone, dan menambahkan remote `upstream` secara otomatis. Jika memakai cara ini, lompat ke bagian 4.

---

## 3. Clone fork ke laptop

Ganti `USERNAME-KAMU` dengan username GitHub-mu.

```bash
git clone https://github.com/USERNAME-KAMU/laravel5d.git
cd laravel5d
```

- **Windows:** jalankan di PowerShell atau Git Bash. Pilih folder kerja dulu, misalnya `cd C:\Users\NamaKamu\Documents`.
- **macOS:** jalankan di Terminal, misalnya setelah `cd ~/Documents`.

## 4. Hubungkan ke repo dosen (`upstream`)

```bash
git remote add upstream https://github.com/mirzayogy/laravel5d.git
git remote -v
```

Hasil yang benar:
```
origin    https://github.com/USERNAME-KAMU/laravel5d.git (fetch)
origin    https://github.com/USERNAME-KAMU/laravel5d.git (push)
upstream  https://github.com/mirzayogy/laravel5d.git (fetch)
upstream  https://github.com/mirzayogy/laravel5d.git (push)
```

---

## 5. Jalankan proyek Laravel

```bash
composer install
npm install
cp .env.example .env          # Windows PowerShell: copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev                   # terminal 1
php artisan serve             # terminal 2
```

Buka <http://localhost:8000>.

---

## 6. Alur kerja harian: pakai branch

Jangan bekerja langsung di `main`. Buat branch baru untuk setiap pekerjaan:

```bash
git switch -c feature/nama-fitur
```

Setelah mengubah kode:

```bash
git status                                 # lihat file yang berubah
git add .                                  # siapkan semua perubahan
git commit -m "feat: deskripsi singkat"    # simpan perubahan
git push -u origin feature/nama-fitur      # kirim ke fork-mu
```

Untuk `push` berikutnya di branch yang sama, cukup `git push`.

### Membuat Pull Request (PR) ke repo dosen

PR adalah cara mengirim pekerjaanmu ke repo dosen supaya beliau bisa melihat, mengomentari, dan menerimanya. Kamu tidak `push` ke repo dosen. Kamu `push` ke fork-mu sendiri, lalu meminta dosen mengambil perubahannya.

**Sebelum membuat PR, pastikan:**
- Semua perubahan sudah di-`commit` dan di-`push` ke branch di fork-mu.
- Proyek berjalan: `php artisan migrate:fresh --seed` tidak error.
- File `.env` dan folder `vendor/` tidak ikut ter-commit.

#### Format judul dan deskripsi PR

Dosen memeriksa banyak PR, jadi buat judul dan deskripsi dalam **bahasa Inggris** yang langsung menjelaskan tugas ke berapa, siapa pemiliknya, apa yang dikerjakan, dan apa buktinya. Jika dosen sudah menentukan format sendiri, ikuti format beliau.

**Judul:**
```
[Assignment N] Assignment Topic - Full Name - NPM - Class
```
Contoh:
```
[Assignment 1] Table Relationships: Habit Tracker - Muhammad Dzakwan Najmi - 2410010454 - TI 5C REG BJB
```
Untuk tugas berikutnya cukup ganti nomor dan topiknya. Judul boleh diubah kapan saja lewat tombol **Edit** atau `gh pr edit <nomor> --repo <repo-dosen> --title "..."`.

**Deskripsi (bahasa Inggris):** gunakan kerangka berikut.
```markdown
## Assignment
Assignment N: assignment name.

| | |
|---|---|
| **Student** | Nama |
| **NPM** | ... |
| **Class** | ... |
| **Phase** | P01: nama fase |
| **Status** | Done / In progress (tanggal) |
| **Fork / branch** | link fork dan nama branch |

## What was done
| Job | Description | Status |
|---|---|---|
| J1 | ... | Done |

## Proof
- Progress report: link ke file di docs/progress
- Link ke folder atau file penting di fork-mu
- Link ke commit

## How to verify
Perintah untuk menjalankan dan mengecek hasilnya.

## Not done yet
Hal yang sengaja belum dikerjakan.
```

**Tips:**
- Semua bukti berupa **link ke repositori-mu** (file, folder, atau commit), bukan tangkapan layar saja.
- Untuk PR yang sama, perbarui deskripsi lewat **Edit** saat status berubah. Jangan buat PR baru.
- Catat progres setiap fase di `docs/progress/Pxx-nama-fase.md` (lihat bagian *Mencatat progres* di bawah), dan pakai kode job di pesan commit, misalnya `P01-J3: add Habit relationships`.

#### Cara 1: lewat website
1. Buka fork-mu di GitHub. Setelah `push`, muncul banner kuning **Compare & pull request**. Klik banner itu.
   Jika banner tidak muncul, buka tab **Pull requests** lalu klik **New pull request**.
2. Periksa empat kotak di bagian atas:
   - **base repository:** repo dosen (`mirzayogy/laravel5d`)
   - **base:** `main`
   - **head repository:** fork-mu
   - **compare:** branch kerjamu, misalnya `feature/nama-fitur`
3. Isi **judul**. Gunakan format yang jelas dan sertakan identitasmu, misalnya:
   `[Assignment N] Topic - Your Name - NPM - Class` (lihat bagian format di atas)
4. Isi **deskripsi**: ringkasan pekerjaan, apa yang sudah dites, dan identitasmu.
5. Klik **Create pull request**.

#### Cara 2: lewat terminal
```bash
gh pr create \
  --repo mirzayogy/laravel5d \
  --base main \
  --head USERNAME-KAMU:feature/nama-fitur \
  --title "[Assignment N] Topic - Your Name - NPM - Class" \
  --body "Ringkasan pekerjaan dan cara mengetesnya."
```
Di Windows PowerShell, tulis perintah dalam satu baris atau ganti `\` di akhir baris dengan tanda backtick (`` ` ``).

Setelah berhasil, terminal menampilkan link PR. Kirim link itu ke dosen jika beliau memintanya.

#### Setelah PR dibuat
- **Revisi:** jika dosen meminta perubahan, kerjakan di branch yang sama, lalu `git add .`, `git commit`, dan `git push`. PR ikut ter-update otomatis. Jangan membuat PR baru.
- **Komentar:** balas komentar dosen langsung di halaman PR.
- **Status:** PR bisa berstatus *Open*, *Merged* (diterima), atau *Closed* (ditutup).
- **Jangan menghapus branch** sebelum PR selesai, karena PR akan ikut rusak.
- **Jangan mengganti nama branch** yang sudah dipakai PR.

#### Kesalahan umum pada PR
| Masalah | Solusi |
|---|---|
| Tidak ada tombol **Compare & pull request** | Buka tab **Pull requests**, lalu **New pull request**, dan pilih branch-mu di **compare**. |
| PR berisi banyak file yang bukan buatanmu | Fork-mu tertinggal dari repo dosen. Ambil pembaruan dulu (bagian 7), lalu `push` lagi. |
| Muncul *This branch has conflicts* | Selesaikan conflict seperti di bagian 7, lalu `push`. |
| Salah memilih base atau head | Klik **Edit** di samping judul PR untuk mengganti base, atau tutup PR dan buat ulang. |
| `gh pr create` meminta login | Jalankan `gh auth login`. |

### Mencatat progres (fase `Pxx` dan job `Jx`)

Selain PR, catat progresmu di repo supaya dosen bisa melihat apa yang sudah selesai, buktinya, dan kapan selesai. Contoh nyata: [`docs/progress/P01-database-design.md`](../progress/P01-database-design.md).

**Aturan penamaan**

| Kode | Arti | Contoh |
|---|---|---|
| `Pxx` | Satu fase pekerjaan, satu file | `P01` database design, `P02` authentication |
| `Jx` | Satu job di dalam fase | `J1` ERD, `J2` migrations |
| Nama file | `docs/progress/Pxx-nama-fase.md` | `docs/progress/P01-database-design.md` |

**Arti status**

| Status | Arti |
|---|---|
| ⏳ Planned | Belum dimulai |
| 🚧 In progress | Sedang dikerjakan |
| ✅ Done | Selesai dan ada bukti |
| ⛔ Blocked | Terhambat, tulis alasannya di catatan |

**Kerangka file progres (bahasa Inggris)**

````markdown
# P01: Phase Title

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | YYYY-MM-DD |
| **Completed** | YYYY-MM-DD |
| **Branch** | `feature/nama-branch` |
| **Pull request** | link PR |

## Goal
Apa yang ingin dicapai fase ini.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Job title | ✅ Done | YYYY-MM-DD | link |
| J2 | Job title | 🚧 In progress | - | - |

---

### J1: Job title
- **Status:** ✅ Done, YYYY-MM-DD
- **What:** Apa yang dikerjakan.
- **Proof:** link ke file, folder, atau commit di fork-mu.
- **Verified:** perintah atau hasil pengecekan.
- **Not done:** hal yang belum dikerjakan.

## Next
Fase berikutnya atau job yang tersisa.
````

**Cara memperbarui**
1. Saat mulai mengerjakan job, ubah statusnya menjadi 🚧 In progress.
2. Setelah selesai, isi bukti (link ke commit, file, atau folder di fork-mu), ubah menjadi ✅ Done, dan isi tanggal selesai di tabel dan di detail job.
3. Tulis kode job di pesan commit, misalnya `P01-J3: add Habit relationships`.
4. `push` ke branch yang sama. PR ikut ter-update, lalu perbarui bagian **What was done** dan **Status** di deskripsi PR lewat **Edit**.
5. Untuk fase berikutnya, buat file baru `P02-nama-fase.md`. Jangan menimpa file fase sebelumnya.

Gunakan link ke commit yang sudah di-push sebagai bukti. Link ke commit yang belum di-push tidak bisa dibuka dosen.

---

## 7. Mengambil pembaruan dari repo dosen

Jika dosen menambahkan materi atau perubahan baru:

```bash
git switch main
git fetch upstream
git merge upstream/main
git push origin main
```

Jika ingin membawa pembaruan itu ke branch kerjamu:
```bash
git switch feature/nama-fitur
git merge main
```

Jika muncul **conflict**, Git menandai file yang bentrok dengan `<<<<<<<`, `=======`, dan `>>>>>>>`. Edit file itu, hapus tanda-tandanya, pilih kode yang benar, lalu:
```bash
git add .
git commit
```

---

## 8. Masalah umum

| Masalah | Solusi |
|---|---|
| `git: command not found` / `'git' is not recognized` | Git belum terpasang atau terminal belum dibuka ulang. |
| `Authentication failed` saat `push` | Jalankan `gh auth login`. GitHub tidak menerima password akun, gunakan login browser atau Personal Access Token. |
| `remote upstream already exists` | Sudah pernah ditambahkan. Cek dengan `git remote -v`. |
| `Permission denied` saat `push` ke repo dosen | Kamu memang tidak boleh `push` ke repo dosen. Lakukan `push` ke `origin` (fork-mu), lalu buat Pull Request. |
| `php artisan migrate` gagal karena driver | Pastikan ekstensi `pdo_sqlite` aktif, atau atur `DB_CONNECTION` di `.env`. |
| `composer install` sangat lambat atau gagal | Cek koneksi internet, lalu jalankan ulang. |
| File `.env` ikut ter-commit | Jangan. File ini sudah ada di `.gitignore`, jadi jangan pakai `git add -f`. |

## 9. Ringkasan perintah

```bash
gh repo fork mirzayogy/laravel5d --clone   # fork + clone
git remote add upstream <url-dosen>         # sekali saja
git switch -c feature/xxx                   # branch baru
git add . && git commit -m "pesan"          # simpan perubahan
git push -u origin feature/xxx              # kirim ke fork
gh pr create --repo mirzayogy/laravel5d --base main --head USERNAME:feature/xxx   # buka PR
git fetch upstream && git merge upstream/main   # ambil pembaruan dosen
```
