<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Models\Gallery;
use App\Models\Stylist;

use App\Http\Controllers\BookingController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\LocationController;

use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PowerBiBookingController;

/*
|--------------------------------------------------------------------------
| REDIRECT AWAL
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/home');

/*
|--------------------------------------------------------------------------
| REDIRECT SETELAH LOGIN
|--------------------------------------------------------------------------
| Admin -> /admin/dashboard
| User  -> /home
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->get('/dashboard', function () {
    if (Auth::user() && Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| PUBLIC PAGE
|--------------------------------------------------------------------------
*/

Route::get('/home', function () {
    $galleries = Gallery::latest()->take(6)->get();
    $stylists = Stylist::latest()->get();

    return view('home', compact('galleries', 'stylists'));
})->name('home');

Route::get('/penata-rias', function () {
    $stylists = Stylist::latest()->get();

    return view('penata-rias', compact('stylists'));
})->name('penata-rias');

Route::get('/galeri', function () {
    $galleries = Gallery::latest()->get();

    return view('galeri', compact('galleries'));
})->name('galeri');

Route::get('/tentang-kami', function () {
    return view('tentang-kami');
})->name('tentang-kami');

/*
|--------------------------------------------------------------------------
| PESAN / BOOKING USER
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/pesan-sekarang', [BookingController::class, 'create'])
        ->name('pesan');

    Route::post('/pesan-sekarang', [BookingController::class, 'store'])
        ->name('pesan.store');

    Route::post('/pesan-sekarang/reverse-location', [LocationController::class, 'reverseGeocode'])
        ->name('location.reverse');

    Route::get('/riwayat', [BookingController::class, 'riwayat'])
        ->name('riwayat');

    Route::get('/notifikasi', [BookingController::class, 'notifications'])
        ->name('notifikasi');
});

/*
|--------------------------------------------------------------------------
| GOOGLE CALENDAR ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/google/auth', [GoogleCalendarController::class, 'redirectToGoogle'])
        ->name('google.auth');

    Route::get('/google/callback', [GoogleCalendarController::class, 'handleGoogleCallback'])
        ->name('google.callback');
});

/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | REDIRECT /admin KE DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::redirect('/', '/admin/dashboard');

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD ADMIN
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | POWER BI / CSV BOOKING
        |--------------------------------------------------------------------------
        */

        Route::get('/powerbi/bookings.csv', [PowerBiBookingController::class, 'bookingsCsv'])
            ->name('powerbi.bookings.csv');

        /*
        |--------------------------------------------------------------------------
        | BOOKING ADMIN
        |--------------------------------------------------------------------------
        */

        Route::get('/bookings', [AdminBookingController::class, 'index'])
            ->name('bookings.index');

        Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])
            ->name('bookings.updateStatus');

        Route::patch('/bookings/{booking}/approve', [AdminBookingController::class, 'approve'])
            ->name('bookings.approve');

        Route::patch('/bookings/{booking}/pending', [AdminBookingController::class, 'pending'])
            ->name('bookings.pending');

        Route::patch('/bookings/{booking}/reject', [AdminBookingController::class, 'reject'])
            ->name('bookings.reject');

        Route::delete('/bookings/{booking}', [AdminBookingController::class, 'destroy'])
            ->name('bookings.destroy');

        /*
        |--------------------------------------------------------------------------
        | GALLERY ADMIN
        |--------------------------------------------------------------------------
        */

        Route::resource('gallery', GalleryController::class);

        /*
        |--------------------------------------------------------------------------
        | NOTIFIKASI ADMIN
        |--------------------------------------------------------------------------
        */

        Route::get('/notifikasi', [BookingController::class, 'notifications'])
            ->name('notifications');
    });

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
