<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Clubs\ClubController;
use App\Http\Controllers\Clubs\ClubRequestController;
use App\Http\Controllers\Clubs\ClubJoinRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');
Route::view('/landing', 'landing')->name('landing.page');

//Route::get('/home', [DashboardController::class, 'index'])
//   ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

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

        Route::post('/presence/heartbeat', [PresenceController::class, 'heartbeat'])->name('presence.heartbeat');
        Route::get('/presence/status', [PresenceController::class, 'index'])->name('presence.index');

        // Fitur Pesan / Messages
        Route::get('/messages/{conversation}/updates', [MessageController::class, 'updates'])->name('messages.updates');
        Route::get('/messages/{conversation?}', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{conversation}/show', [MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');

        Route::prefix('clubs')->name('clubs.')->group(function () {
            Route::get('/', [ClubController::class, 'index'])->name('index');
            Route::get('/request', [ClubRequestController::class, 'request'])->name('request');
            Route::get('/request/list', [ClubRequestController::class, 'listRequest'])->name('request.list');
            Route::get('/request/list/{request}', [ClubRequestController::class, 'detail'])->name('request.detail');
            Route::post('/request/store', [ClubRequestController::class, 'storeRequest'])->name('request.store');
            Route::delete('/request/{request}/cancel', [ClubRequestController::class, 'destroylRequest'])->name('request.cancel');
            Route::get('/{club}/activity/{activity}', [ClubController::class, 'showActivity'])->name('activity.show');
            Route::get('/{club}', [ClubController::class, 'show'])->name('show');
            Route::get('/{club}/settings', [ClubController::class, 'settings'])->name('settings');
            Route::put('/{club}/settings', [ClubController::class, 'update'])->name('update');
            Route::post('/{club}/join', [ClubJoinRequestController::class, 'join'])->name('join');
            Route::post('/{club}/join/request', [ClubJoinRequestController::class, 'storeRequest'])->name('join.request');
            Route::delete('/{club}/join/{request}/cancel', [ClubJoinRequestController::class, 'cancelRequest'])->name('join.request.cancel');
            Route::patch('/{club}/settings/join/{request}/accept', [ClubJoinRequestController::class, 'acceptRequest'])->name('join.request.accept');
            Route::patch('/{club}/settings/join/{request}/reject', [ClubJoinRequestController::class, 'rejectRequest'])->name('join.request.reject');
            Route::patch('/{club}/promote/{userId}', [ClubController::class, 'promoteModerator'])->name('promote');
            Route::patch('/{club}/demote/{userId}', [ClubController::class, 'demoteModerator'])->name('demote');
            Route::patch('/{club}/settings/visibility', [ClubController::class, 'updateVisibility'])->name('visibility.update');
            Route::patch('/{club}/settings/approval', [ClubController::class, 'updateApproval'])->name('approval.update');
            Route::delete('/{club}/leave', [ClubController::class, 'leave'])->name('leave');
            Route::delete('/clubs/{club}/kick/{userId}', [ClubController::class, 'kickMember'])->name('kick');
            Route::delete('/clubs/{club}/delete', [ClubController::class, 'deleteClub'])->name('delete');
        });

        // Fitur Postingan / Posts
        Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
        Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
        Route::post('/posts/{post}/comments', [PostController::class, 'storeComment'])->name('posts.comments.store');

        Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
        // Fitur Notifikasi
        Route::get('/notifications/updates', [NotificationController::class, 'updates'])->name('notifications.updates');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
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
});
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
