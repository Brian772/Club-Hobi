<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function storeLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
            ? back()->with('status', 'Tautan reset password telah dikirim ke email Anda!')
            : back()->withErrors(['email' => __($status)]);
    }

    public function edit(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'request' => $request,
            'token' => $token,
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password_hash' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($status)]);
        }

        Auth::logout();

        return redirect()->route('login')
            ->with('status', 'Password berhasil diubah. Silakan login kembali.');
    }

    public function editSettingsPassword(Request $request): View
    {
        return view('settings.edit-password');
    }

    public function updateSettingsPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $request->user()->forceFill([
            'password_hash' => Hash::make($request->password),
        ])->save();

        return redirect()->route('settings.account')->with('success', 'Password berhasil di perbarui');
    }
}