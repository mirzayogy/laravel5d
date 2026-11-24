<?php

use App\Http\Controllers\PeriodeController;
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
