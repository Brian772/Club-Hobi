<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'TA.landing')->name('landing');
Route::view('/landing', 'TA.landing')->name('landing.page');

Route::get('/dashboard', function () {
    return view('mobile.dashboard');
})->middleware(['auth'])->name('dashboard');

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

<<<<<<< Updated upstream
Route::get('/mobile/navigation', function () {
    return view('mobile.navigation');
})->name('mobile.navigation');
=======
    Route::get('/messages/{conversation?}', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}/show', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');

    Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
    Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');
    Route::post('/clubs/{club}/join', [ClubController::class, 'join'])->name('clubs.join');
    Route::delete('/clubs/{club}/leave', [ClubController::class, 'leave'])->name('clubs.leave');

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
});
>>>>>>> Stashed changes

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';