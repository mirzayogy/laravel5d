<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/', [MenuController::class, 'index'])->name('home');
Route::get('/menu/{menu}', [MenuController::class, 'show'])->name('menus.show');

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale');

// Guests
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Signed-in users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
    Route::post('/email/verify-demo', [VerificationController::class, 'demo'])->name('verification.demo');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/state', [OrderController::class, 'state'])->name('orders.state');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
});

// Customers
Route::middleware(['auth', EnsureUserHasRole::class.':customer'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->middleware('verified')->name('orders.store');
    Route::get('/orders/{order}/pay', [OrderController::class, 'payment'])->name('orders.payment');
    Route::post('/orders/{order}/pay/method', [OrderController::class, 'chooseMethod'])->name('orders.payment.method');
    Route::post('/orders/{order}/pay', [OrderController::class, 'confirmPayment'])->name('orders.payment.confirm');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// Cashiers and admins
Route::middleware(['auth', EnsureUserHasRole::class.':cashier,admin'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/orders', [Staff\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/poll', [Staff\OrderController::class, 'poll'])->name('orders.poll');
        Route::post('/orders/{order}/pay', [Staff\OrderController::class, 'pay'])->name('orders.pay');
        Route::patch('/orders/{order}/status', [Staff\OrderController::class, 'updateStatus'])->name('orders.status');
    });

// Admins
Route::middleware(['auth', EnsureUserHasRole::class.':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::patch('menus/{menu}/availability', [Admin\MenuController::class, 'toggle'])->name('menus.toggle');
        Route::resource('menus', Admin\MenuController::class)->except('show');
        Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('tags', Admin\TagController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [Admin\ReportController::class, 'export'])->name('reports.export');
        Route::resource('users', Admin\UserController::class)->except('show');
        Route::delete('orders/{order}', [Admin\OrderController::class, 'destroy'])->name('orders.destroy');
    });
