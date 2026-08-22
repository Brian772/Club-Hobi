<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\SettingsController;

// Halaman utama (Welcome Page)
Route::get('/', function () {
    return view('welcome');
});

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

   Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'settings'])
        ->name('index');
    Route::get('/profile', [SettingsController::class, 'profilesettings'])
        ->name('profile');
    // Update Nama + Bio
    Route::post('/profile/update', [SettingsController::class, 'updateProfile'])
        ->name('profile.update');
    // Ganti / Upload Foto
    Route::post('/profile/avatar', [SettingsController::class, 'updateAvatar'])
        ->name('profile.avatar');
    // Hapus Foto
    Route::delete('/profile/avatar', [SettingsController::class, 'deleteAvatar'])
        ->name('profile.avatar.delete');
    // Tambah Hobi
    Route::post('/profile/hobby', [SettingsController::class, 'addHobby'])
        ->name('profile.hobby.add');
    Route::delete('/profile/hobby/{clubId}', [SettingsController::class, 'deleteHobby'])
        ->name('profile.hobby.delete');
    Route::get('/account', [SettingsController::class, 'accountsettings'])
        ->name('account');

});

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';