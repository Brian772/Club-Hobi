<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Clubs\ClubController;
use App\Http\Controllers\Clubs\ClubRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');
Route::view('/landing', 'landing')->name('landing.page');

Route::get('/home', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::get('/mobile/dashboard', function () {
    return redirect()->route('dashboard');
})->name('mobile.dashboard');

Route::get('/mobile/club', function () {
    return redirect()->route('clubs.index');
})->name('mobile.club');

Route::get('/mobile/loading', function () {
    return redirect()->route('landing');
})->name('mobile.loading');

Route::get('/mobile/notification', function () {
    return redirect()->route('notifications.index');
})->name('mobile.notification');

Route::get('/mobile/message', function () {
    return redirect()->route('messages.index');
})->name('mobile.message');

Route::get('/mobile/navigation', function () {
    return redirect()->route('dashboard');
})->name('mobile.navigation');

// Route yang membutuhkan login (Auth Middleware)
Route::middleware('auth')->group(function () {
    
    // Fitur Pesan / Messages
    Route::get('/messages/{conversation?}', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}/show', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');

    // Fitur Klub / Clubs
    Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
    Route::get('/clubs/request', [ClubRequestController::class, 'request'])->name('clubs.request');
    Route::get('/clubs/request/list', [ClubRequestController::class, 'listRequest'])->name('clubs.request.list');
    Route::get('/clubs/request/{id}', [ClubRequestController::class, 'detail'])->name('clubs.request.detail');
    Route::post('/clubs/request', [ClubRequestController::class, 'storeRequest'])->name('clubs.request.store');
    Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');
    Route::post('/clubs/{club}/join', [ClubController::class, 'join'])->name('clubs.join');
    Route::delete('/clubs/{club}/leave', [ClubController::class, 'leave'])->name('clubs.leave');

    // Fitur Postingan / Posts
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
    Route::post('/posts/{post}/comments', [PostController::class, 'storeComment'])->name('posts.comments.store');

    // Fitur Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Fitur Pengaturan / Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'settings'])->name('index');
        Route::get('/profile', [SettingsController::class, 'profilesettings'])->name('profile');
        Route::post('/profile/update', [SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/avatar', [SettingsController::class, 'updateAvatar'])->name('profile.avatar');
        Route::delete('/profile/avatar', [SettingsController::class, 'deleteAvatar'])->name('profile.avatar.delete');
        Route::post('/profile/hobby', [SettingsController::class, 'addHobby'])->name('profile.hobby.add');
        Route::delete('/profile/hobby/{clubId}', [SettingsController::class, 'deleteHobby'])->name('profile.hobby.delete');
        Route::get('/account', [SettingsController::class, 'accountsettings'])->name('account');
    });

    // Fitur Profil / Profile bawaan Laravel
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
