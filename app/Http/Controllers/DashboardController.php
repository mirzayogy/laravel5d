<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pembayaran;
use App\Models\Penyewaan;
use App\Models\Periode;

class DashboardController extends Controller
{
    public function index()
    {
        $ringkasan = [
            ['nama' => 'Kategori',   'jumlah' => Kategori::count(),   'warna' => 'bg-info',    'ikon' => 'fas fa-tags'],
            ['nama' => 'Barang',     'jumlah' => Barang::count(),     'warna' => 'bg-success', 'ikon' => 'fas fa-campground'],
            ['nama' => 'Periode',    'jumlah' => Periode::count(),    'warna' => 'bg-secondary', 'ikon' => 'fas fa-calendar-alt'],
            ['nama' => 'Penyewaan',  'jumlah' => Penyewaan::count(),  'warna' => 'bg-warning', 'ikon' => 'fas fa-shopping-cart'],
            ['nama' => 'Pembayaran', 'jumlah' => Pembayaran::count(), 'warna' => 'bg-danger',  'ikon' => 'fas fa-money-bill'],
        ];

        return view('dashboard', compact('ringkasan'));
    }
}