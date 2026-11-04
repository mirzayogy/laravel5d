<?php

use App\Http\Controllers\TokoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TokoController::class, 'index'])->name('toko.index');
Route::get('/produk', [TokoController::class, 'produk'])->name('toko.produk');
Route::get('/pesanan', [TokoController::class, 'pesanan'])->name('toko.pesanan');
