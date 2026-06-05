<?php

use Illuminate\Support\Facades\Route;

use App\Models\Gallery;
use App\Models\Stylist;

use App\Http\Controllers\BookingController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PowerBiBookingController;

/*
|--------------------------------------------------------------------------
| REDIRECT
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/home');


/*
|--------------------------------------------------------------------------
| PUBLIC PAGE
|--------------------------------------------------------------------------
*/

Route::get('/home', function () {

    $galleries = Gallery::latest()->take(6)->get();
    $stylists  = Stylist::latest()->get();

    return view('home', compact('galleries', 'stylists'));

})->name('home');


/*
|--------------------------------------------------------------------------
| PENATA RIAS
|--------------------------------------------------------------------------
*/

Route::get('/penata-rias', function () {

    $stylists = Stylist::latest()->get();

    return view('penata-rias', compact('stylists'));

})->name('penata-rias');


/*
|--------------------------------------------------------------------------
| GALERI
|--------------------------------------------------------------------------
*/

Route::get('/galeri', function () {

    $galleries = Gallery::latest()->get();

    return view('galeri', compact('galleries'));

})->name('galeri');


/*
|--------------------------------------------------------------------------
| TENTANG KAMI
|--------------------------------------------------------------------------
*/

Route::get('/tentang-kami', function () {

    return view('tentang-kami');

})->name('tentang-kami');


/*
|--------------------------------------------------------------------------
| PESAN USER
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/pesan-sekarang', [BookingController::class, 'create'])
        ->name('pesan');

    Route::post('/pesan-sekarang', [BookingController::class, 'store'])
        ->name('pesan.store');

    Route::get('/riwayat', [BookingController::class, 'riwayat'])
        ->name('riwayat');

    Route::get('/notifikasi', [BookingController::class, 'notifications'])
        ->name('notifikasi');

});


/*
|--------------------------------------------------------------------------
| GOOGLE CALENDAR
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
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::redirect('/', '/admin/dashboard');

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | REDIRECT ADMIN KE DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::redirect('/', '/admin/dashboard');


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD ADMIN / POWER BI
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/powerbi/bookings.csv', [PowerBiBookingController::class, 'bookingsCsv'])
            ->name('powerbi.bookings.csv');


        /*
        |--------------------------------------------------------------------------
        | GALLERY ADMIN
        |--------------------------------------------------------------------------
        */

        Route::resource('gallery', GalleryController::class);


        /*
        |--------------------------------------------------------------------------
        | BOOKING ADMIN
        |--------------------------------------------------------------------------
        */

        Route::get('/bookings', [AdminBookingController::class, 'index'])
            ->name('bookings.index');

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
        | NOTIFIKASI ADMIN
        |--------------------------------------------------------------------------
        */

        Route::get('/notifikasi', [BookingController::class, 'notifications'])
            ->name('notifications');

    });
