<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'TA.landing')->name('landing');
Route::view('/landing', 'TA.landing')->name('landing.page');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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