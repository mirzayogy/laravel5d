<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\HintUnlockController;
use App\Http\Controllers\HomeController;
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
Route::get('/writeups', [WriteupController::class, 'index'])->name('writeups.index');

// Guests
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Signed-in users
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/team', [TeamController::class, 'show'])->name('team.show');
    Route::post('/team', [TeamController::class, 'store'])->name('team.store');
    Route::post('/team/join', [TeamController::class, 'join'])->name('team.join');
    Route::delete('/team/leave', [TeamController::class, 'leave'])->name('team.leave');

    Route::post('/competitions/{competition}/register', [CompetitionController::class, 'register'])
        ->name('competitions.register');

    Route::get('/competitions/{competition}/challenges', [ChallengeController::class, 'index'])
        ->name('challenges.index');
    Route::get('/challenges/{challenge}', [ChallengeController::class, 'show'])->name('challenges.show');
    Route::post('/challenges/{challenge}/submit', [SubmissionController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('challenges.submit');
    Route::post('/hints/{hint}/unlock', [HintUnlockController::class, 'store'])->name('hints.unlock');

    Route::get('/challenges/{challenge}/writeups/create', [WriteupController::class, 'create'])
        ->name('writeups.create');
    Route::post('/challenges/{challenge}/writeups', [WriteupController::class, 'store'])
        ->name('writeups.store');
    Route::get('/writeups/{writeup}/edit', [WriteupController::class, 'edit'])->name('writeups.edit');
    Route::put('/writeups/{writeup}', [WriteupController::class, 'update'])->name('writeups.update');
    Route::delete('/writeups/{writeup}', [WriteupController::class, 'destroy'])->name('writeups.destroy');
});

// Writeup detail: published writeups are visible to everyone, drafts only to their author.
Route::get('/writeups/{writeup}', [WriteupController::class, 'show'])->name('writeups.show');

// Admin
Route::middleware(['auth', EnsureUserHasRole::class.':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('competitions', Admin\CompetitionController::class)->except('show');
        Route::resource('challenges', Admin\ChallengeController::class)->except('show');
        Route::post('/challenges/{challenge}/hints', [Admin\HintController::class, 'store'])->name('hints.store');
        Route::delete('/hints/{hint}', [Admin\HintController::class, 'destroy'])->name('hints.destroy');
    });
