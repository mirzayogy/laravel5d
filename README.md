## Assignment
 
Assignment 2: Design Database ERD.
 
| | |
|---|---|
| **Student** | Muhammad Rizky |
| **NPM** | 2410010226 |
| **Class** | 5C |
| **Phase** | P02: Design Database ERD |
| **Status** | Done |
| **Fork / branch** | https://github.com/rizkymhmd06/Tugas-1-PBO-2 / feature/design-database |
 
## What was done
 
| Job | Description | Status |
|---|---|---|
| J1 | Add Name and NPM to README | Done |
| J2 | Add Design Database ERD | Done |
 
## Design Database ERD
 
**Judul Proyek:** Aplikasi E-Commerce Sederhana
 
### Daftar Tabel
 
| No | Tabel | Fungsi |
|---|---|---|
| 1 | users | Data pelanggan |
| 2 | addresses | Alamat pengiriman milik pelanggan |
| 3 | categories | Kategori produk |
| 4 | products | Data produk yang dijual |
| 5 | orders | Data pesanan |
| 6 | order_items | Rincian produk di setiap pesanan |
| 7 | payments | Data pembayaran pesanan |
 
### Struktur Tabel
 
**1. users**
 
| Kolom | Tipe | Key | Keterangan |
|---|---|---|---|
| id | bigint | PK | ID pelanggan |
| name | string | | Nama pelanggan |
| email | string | | Email pelanggan |
| password | string | | Kata sandi (terenkripsi) |
| phone | string | | Nomor telepon |
 
**2. addresses**
 
| Kolom | Tipe | Key | Keterangan |
|---|---|---|---|
| id | bigint | PK | ID alamat |
| user_id | bigint | FK | Mengacu ke users.id |
| recipient_name | string | | Nama penerima |
| address | string | | Alamat lengkap |
| city | string | | Kota |
| postal_code | string | | Kode pos |
 
**3. categories**
 
| Kolom | Tipe | Key | Keterangan |
|---|---|---|---|
| id | bigint | PK | ID kategori |
| name | string | | Nama kategori |
| description | string | | Deskripsi kategori |
 
**4. products**
 
| Kolom | Tipe | Key | Keterangan |
|---|---|---|---|
| id | bigint | PK | ID produk |
| category_id | bigint | FK | Mengacu ke categories.id |
| name | string | | Nama produk |
| description | text | | Deskripsi produk |
| price | decimal | | Harga produk |
| stock | int | | Jumlah stok |
 
**5. orders**
 
| Kolom | Tipe | Key | Keterangan |
|---|---|---|---|
| id | bigint | PK | ID pesanan |
| user_id | bigint | FK | Mengacu ke users.id |
| address_id | bigint | FK | Mengacu ke addresses.id |
| order_date | date | | Tanggal pesanan |
| status | string | | Status pesanan |
| total | decimal | | Total harga |
 
**6. order_items**
 
| Kolom | Tipe | Key | Keterangan |
|---|---|---|---|
| id | bigint | PK | ID rincian pesanan |
| order_id | bigint | FK | Mengacu ke orders.id |
| product_id | bigint | FK | Mengacu ke products.id |
| quantity | int | | Jumlah produk |
| price | decimal | | Harga satuan saat dipesan |
 
**7. payments**
 
| Kolom | Tipe | Key | Keterangan |
|---|---|---|---|
| id | bigint | PK | ID pembayaran |
| order_id | bigint | FK | Mengacu ke orders.id |
| method | string | | Metode pembayaran |
| amount | decimal | | Jumlah dibayar |
| status | string | | Status pembayaran |
| paid_at | datetime | | Waktu pembayaran |
 
### Relasi Antar Tabel
 
| Tabel Asal | Relasi | Tabel Tujuan | Keterangan |
|---|---|---|---|
| users | 1 : N | addresses | Satu pelanggan bisa punya banyak alamat |
| users | 1 : N | orders | Satu pelanggan bisa membuat banyak pesanan |
| addresses | 1 : N | orders | Satu alamat bisa dipakai di banyak pesanan |
| categories | 1 : N | products | Satu kategori memiliki banyak produk |
| orders | 1 : N | order_items | Satu pesanan berisi banyak item |
| products | 1 : N | order_items | Satu produk bisa ada di banyak item pesanan |
| orders | 1 : 1 | payments | Satu pesanan memiliki satu pembayaran |
 
### Gambaran Relasi
 
```
users ----< addresses ----< orders
users ----< orders
categories ----< products ----< order_items >---- orders
orders ----- payments
 
Keterangan: ----< artinya satu ke banyak (1 : N)
            -----  artinya satu ke satu (1 : 1)
```
 
---

--------------------------------------------------------------------------------------

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
