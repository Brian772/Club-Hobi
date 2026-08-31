<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\AppealController;
use App\Http\Controllers\BannedController;
use App\Http\Controllers\ChartController;
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

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');

    Route::prefix('clubs')->name('clubs.')->group(function () {
        Route::get('/', [ClubController::class, 'index'])->name('index');
        Route::get('/{club}', [ClubController::class, 'show'])->name('show');
        Route::post('/{club}/join', [ClubController::class, 'join'])->name('join');
        Route::delete('/{club}/leave', [ClubController::class, 'leave'])->name('leave');
    });

    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
    Route::post('/posts/{post}/comments', [PostController::class, 'storeComment'])->name('posts.comments.store');

    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
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

    Route::get('/banned', [BannedController::class, 'index'])->name('banned');
    Route::get('/appeals', [AppealController::class, 'create'])->name('appeal.create');
    Route::post('/appeals/store', [AppealController::class, 'store'])->name('appeal.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/api/user-charts', [App\Http\Controllers\ChartController::class, 'getDataUsers'])->name('api.chart');

require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
