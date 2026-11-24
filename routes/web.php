<?php

use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\ProdiController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', function () {
    return view('dashboard');
});
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/tables', 'tables')->name('tables');
Route::view('/edit', 'edit')->name('edit');

Route::resource('periode', PeriodeController::class);
Route::get('/laporan/periode', [PeriodeController::class, 'cetak'])->name('laporan.periode');

Route::resource('prodi', ProdiController::class)->except('show');
Route::get('/laporan/prodi', [ProdiController::class, 'cetak'])->name('laporan.prodi');
