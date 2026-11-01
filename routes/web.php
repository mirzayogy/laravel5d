<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReviewController;

Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('merchants', MerchantController::class);
    Route::resource('menus', MenuController::class);
    Route::resource('tags', TagController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'destroy']);
    Route::resource('reviews', ReviewController::class)->only(['index', 'destroy']);
});
