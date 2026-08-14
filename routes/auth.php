<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Halaman register bisa diakses baik oleh guest (step 1 & 2)
// maupun user yang baru saja login di step 2 (untuk menampilkan step 3).
// Logikanya sudah ditangani di dalam RegisteredUserController::create().
Route::get('register/{step?}', [RegisteredUserController::class, 'create'])
    ->name('register');

Route::post('register', [RegisteredUserController::class, 'store'])
    ->name('register.store');

// --- Login/Register dengan SSO ---
Route::get('auth/google', function () {
    abort(501, 'Login dengan Google belum diimplementasikan.');
})->name('auth.google');

Route::get('auth/facebook', function () {
    abort(501, 'Login dengan Facebook belum diimplementasikan.');
})->name('auth.facebook');

Route::middleware('guest')->group(function () {
    // --- Register Multi-Step ---
    Route::post('register/step-1', [RegisteredUserController::class, 'step1'])
        ->name('register.step1');

    Route::post('register/step-2', [RegisteredUserController::class, 'step2'])
        ->name('register.step2');

    // --- Login ---
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.authenticate'); // 👈 Tambahkan ini

    // --- Lupa / Reset Password ---
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // --- Register Step 3 (User Sudah Login dari Step 2) ---
    Route::post('register/step-3', [RegisteredUserController::class, 'step3'])
        ->name('register.step3');

    // --- Verifikasi Email ---
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // --- Konfirmasi & Update Password ---
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    // --- Logout ---
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});