<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // 1. Verifikasi Password
        if (
            !$user ||
            !Hash::check(
                $credentials['password'],
                $user->password_hash
            )
        ) {
            return back()
                ->withErrors([
                    'email' => 'Email atau kata sandi yang Anda masukkan salah.',
                ])
                ->onlyInput('email');
        }

        // 2. Verifikasi Status User
        if ($user->status === 'suspended') {
            $until = $user->suspended_until
                ? $user->suspended_until->format('d M Y H:i')
                : null;

            return back()
                ->withErrors([
                    'email' => 'Akun Anda sedang ditangguhkan' . ($until ? " hingga {$until}." : '.'),
                ])
                ->onlyInput('email');
        }

        if (in_array($user->status, ['banned', 'inactive'], true)) {
            return back()
                ->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan.',
                ])
                ->onlyInput('email');
        }

        // 3. Login & Regenerate Session
        Auth::login($user);
        $request->session()->regenerate();

        // 4. Redirect ke Dashboard (mengakses halaman tujuan awal jika ada)
        return redirect()->intended(route('verification.notice'));
    }

    /**
     * Logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Anda telah keluar.');
    }
}