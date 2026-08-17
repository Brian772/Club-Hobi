<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman utama (Welcome Page)
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/clubs', [App\Http\Controllers\Club\clubController::class, 'index'])->name('clubs.index');
    Route::get('/clubs/{id}', [App\Http\Controllers\Club\clubController::class, 'show'])->name('clubs.show');
    Route::post('/clubs/{id}/join', [App\Http\Controllers\Club\clubController::class, 'join'])->name('clubs.join');
    Route::post('/clubs/{id}/leave', [App\Http\Controllers\Club\clubController::class, 'leave'])->name('clubs.leave');
    Route::delete('/clubs/{id}/member/{userId}', [App\Http\Controllers\Club\clubController::class, 'kickMember'])->name('clubs.kick');
    Route::put('/clubs/{id}', [App\Http\Controllers\Club\clubController::class, 'update'])->name('clubs.update');
});

require __DIR__ . '/auth.php';
