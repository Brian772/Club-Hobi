<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Halaman utama (Welcome Page)
Route::get('/', function () {
    return view('welcome');
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';