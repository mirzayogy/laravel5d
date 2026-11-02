
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Models\User;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Payment;

/*
|--------------------------------------------------------------------------
| HALAMAN UTAMA
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')->name('home');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'loginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');

    Route::get('/register', [AuthController::class, 'registerForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | REDIRECT DASHBOARD BERDASARKAN ROLE
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function () {

        /** @var User $user */
        $user = Auth::user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'groomer' => redirect()->route('groomer.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            default => abort(403, 'Role tidak dikenali.'),
        };

    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/dashboard', function () {

        return view('admin.dashboard', [
            'customers' => User::where('role', 'customer')->count(),

            'groomers' => User::where('role', 'groomer')->count(),

            'pets' => Pet::count(),

            'services' => Service::count(),

            'bookings' => Booking::count(),

            'revenue' => Payment::where('status', 'paid')
                ->sum('amount'),
        ]);

    })->middleware('role:admin')
      ->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD CUSTOMER
    |--------------------------------------------------------------------------
    */

    Route::get('/customer/dashboard', function () {

        /** @var User $user */
        $user = Auth::user();

        return view('customer.dashboard', [

            'pets' => $user->pets()->count(),

            'bookings' => $user->bookings()->count(),

            'latestBookings' => $user->bookings()
                ->with(['pet', 'service'])
                ->latest()
                ->take(5)
                ->get(),

        ]);

    })->middleware('role:customer')
      ->name('customer.dashboard');

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD GROOMER
    |--------------------------------------------------------------------------
    */

    Route::get('/groomer/dashboard', function () {

        /** @var User $user */
        $user = Auth::user();

        return view('groomer.dashboard', [

            'tasks' => $user->groomerBookings()
                ->with(['pet', 'service'])
                ->orderBy('scheduled_at')
                ->get(),

        ]);

    })->middleware('role:groomer')
      ->name('groomer.dashboard');

});
