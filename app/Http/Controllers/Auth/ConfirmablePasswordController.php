<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * Menampilkan halaman konfirmasi password.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Memverifikasi password user.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ]);
        }

        $request->session()->passwordConfirmed();

        return redirect()->intended(route('dashboard'));
    }
}