<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Halaman utama (Welcome Page)
Route::get('/', function () {
    return view('landing');
})->name('home');

// Halaman Dashboard (hanya bisa diakses jika sudah login)
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])
        ->name('profile.dashboard');

    Route::get('/dashboard/posts', [DashboardController::class, 'posts'])
        ->name('posts.dashboard');

    Route::get('/dashboard/club-files', [DashboardController::class, 'clubFiles'])
        ->name('club_files.dashboard');

});

Route::get('/mobile/dashboard', function () {
    return view('mobile.dashboard');
})->name('mobile.dashboard');

Route::get('/mobile/club', function () {
    return view('mobile.club');
})->name('mobile.club');

Route::get('/mobile/loading', function () {
    return view('mobile.loading');
})->name('mobile.loading');


Route::get('/mobile/notification', function () {
    return view('mobile.notification');
})->name('mobile.notification');

Route::get('/mobile/message', function () {
    return view('mobile.message');
})->name('mobile.message');

Route::get('/mobile/navigation', function () {
    return view('mobile.navigation');
})->name('mobile.navigation');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';