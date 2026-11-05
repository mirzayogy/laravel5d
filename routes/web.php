<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('kosts', KostController::class)->only(['index', 'show']);
Route::resource('rooms', RoomController::class)->only(['index', 'show']);
Route::resource('facilities', FacilityController::class)->only(['index', 'show']);
Route::resource('tenants', TenantController::class)->only(['index', 'show']);
Route::resource('bookings', BookingController::class)->only(['index', 'show']);
Route::resource('payments', PaymentController::class)->only(['index', 'show']);