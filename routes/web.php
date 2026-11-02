
<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;

Route::get('/', function () {
    $totalProduk = Product::count();
    $totalKategori = Category::count();
    $totalPesanan = Order::count();
    $totalPelanggan = User::where('role', 'customer')->count();

    return view('dashboard', compact(
        'totalProduk',
        'totalKategori',
        'totalPesanan',
        'totalPelanggan'
    ));
})->name('dashboard');

Route::view('/tables', 'tables')->name('tables');
Route::view('/edit', 'edit')->name('edit');
Route::view('/datatables', 'datatables')->name('datatables');
