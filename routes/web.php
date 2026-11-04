<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Route untuk Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route untuk Accounts
Route::resource('accounts', AccountController::class);

// Route untuk Transactions
Route::resource('transactions', TransactionController::class)->except('show');
