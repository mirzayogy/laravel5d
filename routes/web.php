<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\HintUnlockController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScoreboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\WriteupController;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/', HomeController::class)->name('home');

// Public pages
Route::get('/competitions', [CompetitionController::class, 'index'])->name('competitions.index');
Route::get('/competitions/{competition}', [CompetitionController::class, 'show'])->name('competitions.show');
Route::get('/competitions/{competition}/scoreboard', [ScoreboardController::class, 'show'])->name('scoreboard.show');
Route::get('/competitions/{competition}/scoreboard/data', [ScoreboardController::class, 'data'])->name('scoreboard.data');
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/teams/{team}', [TeamController::class, 'profile'])->name('teams.show');
Route::get('/writeups', [WriteupController::class, 'index'])->name('writeups.index');

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
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');

    Route::get('/team', [TeamController::class, 'show'])->name('team.show');
    Route::post('/team', [TeamController::class, 'store'])->name('team.store');
    Route::put('/team', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/team', [TeamController::class, 'destroy'])->name('team.destroy');
    Route::post('/team/join', [TeamController::class, 'join'])->name('team.join');
    Route::delete('/team/leave', [TeamController::class, 'leave'])->name('team.leave');
    Route::post('/team/code', [TeamController::class, 'regenerateCode'])->name('team.code');
    Route::delete('/team/members/{user}', [TeamController::class, 'removeMember'])->name('team.members.remove');
    Route::post('/team/captain/{user}', [TeamController::class, 'transferCaptain'])->name('team.captain');

    Route::get('/competitions/{competition}/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::get('/challenges/{challenge}', [ChallengeController::class, 'show'])->name('challenges.show');

    Route::post('/writeups/preview', [WriteupController::class, 'preview'])->name('writeups.preview');
    Route::delete('/writeups/{writeup}', [WriteupController::class, 'destroy'])->name('writeups.destroy');
});

// Playing needs a verified email address.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/competitions/{competition}/register', [CompetitionController::class, 'register'])->name('competitions.register');
    Route::post('/challenges/{challenge}/submit', [SubmissionController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('challenges.submit');
    Route::post('/hints/{hint}/unlock', [HintUnlockController::class, 'store'])->name('hints.unlock');

    Route::get('/challenges/{challenge}/writeups/create', [WriteupController::class, 'create'])->name('writeups.create');
    Route::post('/challenges/{challenge}/writeups', [WriteupController::class, 'store'])->name('writeups.store');
    Route::get('/writeups/{writeup}/edit', [WriteupController::class, 'edit'])->name('writeups.edit');
    Route::put('/writeups/{writeup}', [WriteupController::class, 'update'])->name('writeups.update');
});

// Writeup detail: published writeups are visible to everyone, drafts only to their author.
Route::get('/writeups/{writeup}', [WriteupController::class, 'show'])->name('writeups.show');

// Admin
Route::middleware(['auth', EnsureUserHasRole::class.':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::resource('competitions', Admin\CompetitionController::class)->except('show');
        Route::resource('challenges', Admin\ChallengeController::class)->except('show');
        Route::post('/challenges/{challenge}/hints', [Admin\HintController::class, 'store'])->name('hints.store');
        Route::delete('/hints/{hint}', [Admin\HintController::class, 'destroy'])->name('hints.destroy');

        Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [Admin\UserController::class, 'updateRole'])->name('users.role');
        Route::post('/users/{user}/verify', [Admin\UserController::class, 'verify'])->name('users.verify');
        Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/teams', [Admin\TeamController::class, 'index'])->name('teams.index');
        Route::delete('/teams/{team}', [Admin\TeamController::class, 'destroy'])->name('teams.destroy');

        Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('tags', Admin\TagController::class)->only(['index', 'store', 'update', 'destroy']);
    });
