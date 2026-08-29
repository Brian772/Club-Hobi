<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Settings\SettingsController;
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

    Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
    Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');
    Route::post('/clubs/{club}/join', [ClubController::class, 'join'])->name('clubs.join');
    Route::delete('/clubs/{club}/leave', [ClubController::class, 'leave'])->name('clubs.leave');

    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/trash', [PostController::class, 'trash'])->name('posts.trash');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::patch('/posts/{post}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::delete('/posts/{post}/force-delete', [PostController::class, 'forceDelete'])->name('posts.force-delete');
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
    Route::post('/posts/{post}/comments', [PostController::class, 'storeComment'])->name('posts.comments.store');
    Route::delete('/comments/{comment}', [PostController::class, 'destroyComment'])->name('comments.destroy');

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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
    Route::get('/clubs/{id}', [ClubController::class, 'show'])->name('clubs.show');
    Route::post('/clubs/{id}/join', [ClubController::class, 'join'])->name('clubs.join');
    Route::post('/clubs/{id}/leave', [ClubController::class, 'leave'])->name('clubs.leave');
    Route::delete('/clubs/{id}/member/{userId}', [ClubController::class, 'kickMember'])->name('clubs.kick');
    Route::put('/clubs/{id}', [ClubController::class, 'update'])->name('clubs.update');
});

require __DIR__ . '/auth.php';
