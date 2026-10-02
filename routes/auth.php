<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Attribute\RateLimit;

Route::get(
    'register/{step?}',
    [RegisteredUserController::class, 'create']
)->name('register');

Route::post(
        'register/step-3',
        [RegisteredUserController::class, 'step3']
    )->name('register.step3');

Route::middleware('guest')->group(function () {

    Route::post(
        'register/step-1',
        [RegisteredUserController::class, 'step1']
    )->name('register.step1');

    Route::post(
        'register/step-2',
        [RegisteredUserController::class, 'step2']
    )->name('register.step2');

    Route::get(
        'login',
        [AuthenticatedSessionController::class, 'create']
    )->name('login');

    Route::post(
        'login',
        [AuthenticatedSessionController::class, 'store']
    )->name('login.authenticate');

    Route::get('auth/{provider}/redirect', [SocialiteController::class, 'redirectToProvider'])
        ->name('social.redirect');

    Route::get('auth/{provider}/callback', [SocialiteController::class, 'handleProviderCallback'])
        ->name('social.callback');

    Route::get(
        'forgot-password',
        [PasswordResetLinkController::class, 'create']
    )->name('password.request');

    Route::post(
        'forgot-password',
        [PasswordResetLinkController::class, 'store']
    )->name('password.email');

    Route::get(
        'reset-password/{token}',
        [NewPasswordController::class, 'create']
    )->name('password.reset');

    Route::post(
        'reset-password',
        [NewPasswordController::class, 'store']
    )->name('password.store');
});

Route::middleware('auth')->group(function () {

    Route::get('/email/verify', [EmailVerificationPromptController::class, 'index'])->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard')
            ->with('info', 'Email kamu sudah diverifikasi sebelumnya. Tidak perlu melakukan verifikasi ulang.');
        }
        $request->fulfill();

        return redirect()->route('dashboard')
            ->with('success', 'Email kamu berhasil diverifikasi. Terima kasih!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $user = $request->user();
        $key = 'verification-email:' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'resend' => "Tunggu {$seconds} detik sebelum mengirim ulang email verifikasi.",
            ]);
        }

        RateLimiter::hit($key, 60);

        try {
            $user->sendEmailVerificationNotification(); 
        } catch (\Throwable $th) {
            RateLimiter::clear($key);
            return back()->withErrors([
                'resend' => 'Terjadi kesalahan saat mengirim email verifikasi. Silakan coba lagi nanti.',
            ]);
        }

        return back()->with('status', 'verification-link-sent');
    })->name('verification.send');

    Route::get(
        'confirm-password',
        [ConfirmablePasswordController::class, 'show']
    )->name('password.confirm');

    Route::post(
        'confirm-password',
        [ConfirmablePasswordController::class, 'store']
    );

    Route::put(
        'password',
        [PasswordController::class, 'update']
    )->name('password.update');

    Route::post(
        'logout',
        [AuthenticatedSessionController::class, 'destroy']
    )->name('logout');
});