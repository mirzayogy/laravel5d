<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\Admin\OverviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MoodEntryController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\RestDayController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', [LocaleController::class, 'update'])->name('locale.switch');
Route::view('/terms', 'legal.terms')->name('terms');
Route::view('/privacy', 'legal.privacy')->name('privacy');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    // Admin console: the whole system, master data and every user.
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/', [OverviewController::class, 'index'])->name('overview');
        Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('tags', TagController::class)->except('show');
        Route::resource('achievements', AchievementController::class)->except('show');
    });

    Route::middleware('onboarded')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('habits', HabitController::class)->except('show');
        Route::post('/habits/{habit}/restore', [HabitController::class, 'restore'])->name('habits.restore')->whereNumber('habit');
        Route::post('/habits/{habit}/log', [HabitLogController::class, 'store'])->name('habits.log');
        Route::post('/rest-days', [RestDayController::class, 'store'])->name('rest-days.store');
        Route::delete('/rest-days/{date}', [RestDayController::class, 'destroy'])->name('rest-days.destroy')->where('date', '\d{4}-\d{2}-\d{2}');
        Route::get('/reminders/today', [ReminderController::class, 'today'])->name('reminders.today');
        Route::post('/habits/{habit}/reminders', [ReminderController::class, 'store'])->name('reminders.store');
        Route::patch('/reminders/{reminder}', [ReminderController::class, 'update'])->name('reminders.update');
        Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');

        Route::get('/mood', [MoodEntryController::class, 'index'])->name('mood.index');
        Route::post('/mood', [MoodEntryController::class, 'store'])->name('mood.store');
        Route::delete('/mood/{mood}', [MoodEntryController::class, 'destroy'])->name('mood.destroy');

        Route::resource('journal', JournalEntryController::class)->except('show');

        Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
});
