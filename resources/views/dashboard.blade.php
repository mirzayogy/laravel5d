
@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Judul Dashboard -->
    <div class="row mb-3">
        <div class="col-12">
            <h4>Selamat Datang di E-Commerce</h4>
            <p class="text-secondary">
                Berikut ringkasan informasi toko online Anda.
            </p>
        </div>
    </div>

    <!-- Informasi Dashboard -->
    <div class="row">

        <!-- Total Produk -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon text-bg-primary shadow-sm">
                    <i class="bi bi-box-seam"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Produk</span>
                    <span class="info-box-number">{{ $totalProduk }}</span>
                </div>
            </div>
        </div>

        <!-- Total Kategori -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon text-bg-success shadow-sm">
                    <i class="bi bi-tags"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kategori</span>
                    <span class="info-box-number">{{ $totalKategori }}</span>
                </div>
            </div>
        </div>

        <!-- Total Pesanan -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon text-bg-warning shadow-sm">
                    <i class="bi bi-cart-check"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Pesanan</span>
                    <span class="info-box-number">{{ $totalPesanan }}</span>
                </div>
            </div>
        </div>

        <!-- Total Pelanggan -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon text-bg-danger shadow-sm">
                    <i class="bi bi-people"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Pelanggan</span>
                    <span class="info-box-number">{{ $totalPelanggan }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Informasi Sistem -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi E-Commerce</h3>
        </div>
        <div class="card-body">
            <p class="mb-0">
                Selamat datang di halaman administrasi E-Commerce.
                Melalui dashboard ini, administrator dapat memantau
                data produk, kategori, pesanan, dan pelanggan.
            </p>
        </div>
    </div>

</div>

@endsection