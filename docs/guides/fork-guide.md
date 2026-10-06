# Panduan Fork Repo Dosen (Windows & macOS)


Panduan ini menjelaskan cara mengambil repo d
osen, mengerjakan tugas di repo milikmu sendi
ri, dan tetap bisa mengambil pembaruan dari r
epo dosen.

**Istilah singkat**

| Istilah | 
Arti |
|---|---|
| **Fork** | Salinan repo do
sen di akun GitHub-mu. Kamu bebas mengubahnya
 tanpa memengaruhi repo dosen. |
| **Clone** 
| Mengunduh repo fork-mu ke laptop. |
| **`or
igin`** | Repo fork milikmu (tempat kamu `pus
h`). |
| **`upstream`** | Repo asli milik dos
en (tempat kamu `pull` pembaruan). |
| **Bran
ch** | Cabang kerja terpisah, supaya `main` t
etap bersih. |

Repo dosen pada proyek ini: <
https://github.com/mirzayogy/laravel5d>

---


## 1. Persiapan (sekali saja)

### Windows
1
. Buat akun di <https://github.com>.
2. Pasan
g **Git for Windows**: <https://git-scm.com/d
ownload/win>. Pilih opsi bawaan saat instalas
i.
3. Pasang PHP dan Composer lewat PowerShel
l (Run as Administrator):
   ```powershell
  
 Set-ExecutionPolicy Bypass -Scope Process -F
orce; [System.Net.ServicePointManager]::Secur
ityProtocol = [System.Net.ServicePointManager
]::SecurityProtocol -bor 3072; iex ((New-Obje
ct System.Net.WebClient).DownloadString('http
s://php.new/install/windows/8.5'))
   ```
4. 
Pasang **Node.js LTS**: <https://nodejs.org>.

5. Tutup lalu buka ulang PowerShell, dan cek
:
   ```powershell
   git --version
   php -v

   composer -V
   node -v
   ```

### macOS

1. Buat akun di <https://github.com>.
2. Buka
 **Terminal**. Pasang Homebrew jika belum ada
: <https://brew.sh>.
3. Pasang semuanya:
   `
``bash
   brew install git node
   /bin/bash 
-c "$(curl -fsSL https://php.new/install/mac/
8.5)"
   ```
4. Tutup lalu buka ulang Termina
l, dan cek:
   ```bash
   git --version
   ph
p -v
   composer -V
   node -v
   ```

### At
ur identitas Git (Windows & macOS)
```bash
gi
t config --global user.name "Nama Kamu"
git c
onfig --global user.email "email-github-kamu@
example.com"
```

### Login GitHub dari termi
nal (disarankan: GitHub CLI)
- **Windows:** `
winget install --id GitHub.cli`
- **macOS:** 
`brew install gh`

Lalu jalankan `gh auth log
in` dan ikuti petunjuknya (pilih GitHub.com, 
HTTPS, dan login lewat browser).

---

## 2. 
Fork repo dosen

### Lewat website (paling mu
dah)
1. Buka <https://github.com/mirzayogy/la
ravel5d> dan login.
2. Klik tombol **Fork** d
i kanan atas.
3. Pilih akun kamu sebagai **Ow
ner**. Nama repo boleh dibiarkan atau diganti
.
4. Klik **Create fork**.

Sekarang ada sali
nan di `https://github.com/USERNAME-KAMU/lara
vel5d`.

### Lewat terminal (opsional)
```bas
h
gh repo fork mirzayogy/laravel5d --clone
``
`
Perintah ini sekaligus mem-fork, meng-clone
, dan menambahkan remote `upstream` secara ot
omatis. Jika memakai cara ini, lompat ke bagi
an 4.

---

## 3. Clone fork ke laptop

Ganti
 `USERNAME-KAMU` dengan username GitHub-mu.


```bash
git clone https://github.com/USERNAME
-KAMU/laravel5d.git
cd laravel5d
```

- **Win
dows:** jalankan di PowerShell atau Git Bash.
 Pilih folder kerja dulu, misalnya `cd C:\Use
rs\NamaKamu\Documents`.
- **macOS:** jalankan
 di Terminal, misalnya setelah `cd ~/Document
s`.

## 4. Hubungkan ke repo dosen (`upstream
`)

```bash
git remote add upstream https://g
ithub.com/mirzayogy/laravel5d.git
git remote 
-v
```

Hasil yang benar:
```
origin    https
://github.com/USERNAME-KAMU/laravel5d.git (fe
tch)
origin    https://github.com/USERNAME-KA
MU/laravel5d.git (push)
upstream  https://git
hub.com/mirzayogy/laravel5d.git (fetch)
upstr
eam  https://github.com/mirzayogy/laravel5d.g
it (push)
```

---

## 5. Jalankan proyek Lar
avel

```bash
composer install
npm install
cp
 .env.example .env          # Windows PowerSh
ell: copy .env.example .env
php artisan key:g
enerate
php artisan migrate --seed
npm run de
v                   # terminal 1
php artisan 
serve             # terminal 2
```

Buka <htt
p://localhost:8000>.

---

## 6. Alur kerja h
arian: pakai branch

Jangan bekerja langsung 
di `main`. Buat branch baru untuk setiap peke
rjaan:

```bash
git switch -c feature/nama-fi
tur
```

Setelah mengubah kode:

```bash
git 
status                                 # liha
t file yang berubah
git add .                
                  # siapkan semua perubahan
g
it commit -m "feat: deskripsi singkat"    # s
impan perubahan
git push -u origin feature/na
ma-fitur      # kirim ke fork-mu
```

Untuk `
push` berikutnya di branch yang sama, cukup `
git push`.

### Membuat Pull Request (PR) ke 
repo dosen

PR adalah cara mengirim pekerjaan
mu ke repo dosen supaya beliau bisa melihat, 
mengomentari, dan menerimanya. Kamu tidak `pu
sh` ke repo dosen. Kamu `push` ke fork-mu sen
diri, lalu meminta dosen mengambil perubahann
ya.

**Sebelum membuat PR, pastikan:**
- Semu
a perubahan sudah di-`commit` dan di-`push` k
e branch di fork-mu.
- Proyek berjalan: `php 
artisan migrate:fresh --seed` tidak error.
- 
File `.env` dan folder `vendor/` tidak ikut t
er-commit.

#### Format judul dan deskripsi P
R

Dosen memeriksa banyak PR, jadi buat judul
 dan deskripsi dalam **bahasa Inggris** yang 
langsung menjelaskan tugas ke berapa, siapa p
emiliknya, apa yang dikerjakan, dan apa bukti
nya. Jika dosen sudah menentukan format sendi
ri, ikuti format beliau.

**Judul:**
```
[Ass
ignment N] Assignment Topic - Full Name - NPM
 - Class
```
Contoh:
```
[Assignment 1] Table
 Relationships: Habit Tracker - Muhammad Dzak
wan Najmi - 2410010454 - TI 5C REG BJB
```
Un
tuk tugas berikutnya cukup ganti nomor dan to
piknya. Judul boleh diubah kapan saja lewat t
ombol **Edit** atau `gh pr edit <nomor> --rep
o <repo-dosen> --title "..."`.

**Deskripsi (
bahasa Inggris):** gunakan kerangka berikut.

```markdown
## Assignment
Assignment N: assig
nment name.

| | |
|---|---|
| **Student** | 
Nama |
| **NPM** | ... |
| **Class** | ... |

| **Phase** | P01: nama fase |
| **Status** |
 Done / In progress (tanggal) |
| **Fork / br
anch** | link fork dan nama branch |

## What
 was done
| Job | Description | Status |
|---
|---|---|
| J1 | ... | Done |

## Proof
- Pro
gress report: link ke file di docs/progress
-
 Link ke folder atau file penting di fork-mu

- Link ke commit

## How to verify
Perintah u
ntuk menjalankan dan mengecek hasilnya.

## N
ot done yet
Hal yang sengaja belum dikerjakan
.
```

**Tips:**
- Semua bukti berupa **link 
ke repositori-mu** (file, folder, atau commit
), bukan tangkapan layar saja.
- Untuk PR yan
g sama, perbarui deskripsi lewat **Edit** saa
t status berubah. Jangan buat PR baru.
- Cata
t progres setiap fase di `docs/progress/Pxx-n
ama-fase.md` (lihat bagian *Mencatat progres*
 di bawah), dan pakai kode job di pesan commi
t, misalnya `P01-J3: add Habit relationships`
.

#### Cara 1: lewat website
1. Buka fork-mu
 di GitHub. Setelah `push`, muncul banner kun
ing **Compare & pull request**. Klik banner i
tu.
   Jika banner tidak muncul, buka tab **P
ull requests** lalu klik **New pull request**
.
2. Periksa empat kotak di bagian atas:
   -
 **base repository:** repo dosen (`mirzayogy/
laravel5d`)
   - **base:** `main`
   - **head
 repository:** fork-mu
   - **compare:** bran
ch kerjamu, misalnya `feature/nama-fitur`
3. 
Isi **judul**. Gunakan format yang jelas dan 
sertakan identitasmu, misalnya:
   `[Assignme
nt N] Topic - Your Name - NPM - Class` (lihat
 bagian format di atas)
4. Isi **deskripsi**:
 ringkasan pekerjaan, apa yang sudah dites, d
an identitasmu.
5. Klik **Create pull request
**.

#### Cara 2: lewat terminal
```bash
gh p
r create \
  --repo mirzayogy/laravel5d \
  -
-base main \
  --head USERNAME-KAMU:feature/n
ama-fitur \
  --title "[Assignment N] Topic -
 Your Name - NPM - Class" \
  --body "Ringkas
an pekerjaan dan cara mengetesnya."
```
Di Wi
ndows PowerShell, tulis perintah dalam satu b
aris atau ganti `\` di akhir baris dengan tan
da backtick (`` ` ``).

Setelah berhasil, ter
minal menampilkan link PR. Kirim link itu ke 
dosen jika beliau memintanya.

#### Setelah P
R dibuat
- **Revisi:** jika dosen meminta per
ubahan, kerjakan di branch yang sama, lalu `g
it add .`, `git commit`, dan `git push`. PR i
kut ter-update otomatis. Jangan membuat PR ba
ru.
- **Komentar:** balas komentar dosen lang
sung di halaman PR.
- **Status:** PR bisa ber
status *Open*, *Merged* (diterima), atau *Clo
sed* (ditutup).
- **Jangan menghapus branch**
 sebelum PR selesai, karena PR akan ikut rusa
k.
- **Jangan mengganti nama branch** yang su
dah dipakai PR.

#### Kesalahan umum pada PR

| Masalah | Solusi |
|---|---|
| Tidak ada to
mbol **Compare & pull request** | Buka tab **
Pull requests**, lalu **New pull request**, d
an pilih branch-mu di **compare**. |
| PR ber
isi banyak file yang bukan buatanmu | Fork-mu
 tertinggal dari repo dosen. Ambil pembaruan 
dulu (bagian 7), lalu `push` lagi. |
| Muncul
 *This branch has conflicts* | Selesaikan con
flict seperti di bagian 7, lalu `push`. |
| S
alah memilih base atau head | Klik **Edit** d
i samping judul PR untuk mengganti base, atau
 tutup PR dan buat ulang. |
| `gh pr create` 
meminta login | Jalankan `gh auth login`. |


### Mencatat progres (fase `Pxx` dan job `Jx`
)

Selain PR, catat progresmu di repo supaya 
dosen bisa melihat apa yang sudah selesai, bu
ktinya, dan kapan selesai. Contoh nyata: [`do
cs/progress/P01-database-design.md`](../progr
ess/P01-database-design.md).

**Aturan penama
an**

| Kode | Arti | Contoh |
|---|---|---|

| `Pxx` | Satu fase pekerjaan, satu file | `P
01` database design, `P02` authentication |
|
 `Jx` | Satu job di dalam fase | `J1` ERD, `J
2` migrations |
| Nama file | `docs/progress/
Pxx-nama-fase.md` | `docs/progress/P01-databa
se-design.md` |

**Arti status**

| Status | 
Arti |
|---|---|
| ⏳ Planned | Belum dimula
i |
| 🚧 In progress | Sedang dikerjakan |

| ✅ Done | Selesai dan ada bukti |
| ⛔ Bl
ocked | Terhambat, tulis alasannya di catatan
 |

**Kerangka file progres (bahasa Inggris)*
*

````markdown
# P01: Phase Title

| | |
|--
-|---|
| **Status** | ✅ Done |
| **Started*
* | YYYY-MM-DD |
| **Completed** | YYYY-MM-DD
 |
| **Branch** | `feature/nama-branch` |
| *
*Pull request** | link PR |

## Goal
Apa yang
 ingin dicapai fase ini.

## Jobs

| Code | J
ob | Status | Completed | Proof |
|---|---|--
-|---|---|
| J1 | Job title | ✅ Done | YYYY
-MM-DD | link |
| J2 | Job title | 🚧 In pr
ogress | - | - |

---

### J1: Job title
- **
Status:** ✅ Done, YYYY-MM-DD
- **What:** Ap
a yang dikerjakan.
- **Proof:** link ke file,
 folder, atau commit di fork-mu.
- **Verified
:** perintah atau hasil pengecekan.
- **Not d
one:** hal yang belum dikerjakan.

## Next
Fa
se berikutnya atau job yang tersisa.
````

**
Cara memperbarui**
1. Saat mulai mengerjakan 
job, ubah statusnya menjadi 🚧 In progress.

2. Setelah selesai, isi bukti (link ke commi
t, file, atau folder di fork-mu), ubah menjad
i ✅ Done, dan isi tanggal selesai di tabel 
dan di detail job.
3. Tulis kode job di pesan
 commit, misalnya `P01-J3: add Habit relation
ships`.
4. `push` ke branch yang sama. PR iku
t ter-update, lalu perbarui bagian **What was
 done** dan **Status** di deskripsi PR lewat 
**Edit**.
5. Untuk fase berikutnya, buat file
 baru `P02-nama-fase.md`. Jangan menimpa file
 fase sebelumnya.

Gunakan link ke commit yan
g sudah di-push sebagai bukti. Link ke commit
 yang belum di-push tidak bisa dibuka dosen.


---

## 7. Mengambil pembaruan dari repo dos
en

Jika dosen menambahkan materi atau peruba
han baru:

```bash
git switch main
git fetch 
upstream
git merge upstream/main
git push ori
gin main
```

Jika ingin membawa pembaruan it
u ke branch kerjamu:
```bash
git switch featu
re/nama-fitur
git merge main
```

Jika muncul
 **conflict**, Git menandai file yang bentrok
 dengan `<<<<<<<`, `=======`, dan `>>>>>>>`. 
Edit file itu, hapus tanda-tandanya, pilih ko
de yang benar, lalu:
```bash
git add .
git co
mmit
```

---

## 8. Masalah umum

| Masalah 
| Solusi |
|---|---|
| `git: command not foun
d` / `'git' is not recognized` | Git belum te
rpasang atau terminal belum dibuka ulang. |
|
 `Authentication failed` saat `push` | Jalank
an `gh auth login`. GitHub tidak menerima pas
sword akun, gunakan login browser atau Person
al Access Token. |
| `remote upstream already
 exists` | Sudah pernah ditambahkan. Cek deng
an `git remote -v`. |
| `Permission denied` s
aat `push` ke repo dosen | Kamu memang tidak 
boleh `push` ke repo dosen. Lakukan `push` ke
 `origin` (fork-mu), lalu buat Pull Request. 
|
| `php artisan migrate` gagal karena driver
 | Pastikan ekstensi `pdo_sqlite` aktif, atau
 atur `DB_CONNECTION` di `.env`. |
| `compose
r install` sangat lambat atau gagal | Cek kon
eksi internet, lalu jalankan ulang. |
| File 
`.env` ikut ter-commit | Jangan. File ini sud
ah ada di `.gitignore`, jadi jangan pakai `gi
t add -f`. |

## 9. Ringkasan perintah

```ba
sh
gh repo fork mirzayogy/laravel5d --clone  
 # fork + clone
git remote add upstream <url-
dosen>         # sekali saja
git switch -c fe
ature/xxx                   # branch baru
git
 add . && git commit -m "pesan"          # si
mpan perubahan
git push -u origin feature/xxx
              # kirim ke fork
gh pr create --
repo mirzayogy/laravel5d --base main --head U
SERNAME:feature/xxx   # buka PR
git fetch ups
tream && git merge upstream/main   # ambil pe
mbaruan dosen
```


