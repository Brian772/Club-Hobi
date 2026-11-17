<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->route('id'));

        $hash = $request->route('hash');

        if (!hash_equals(
            sha1($user->getEmailForVerification()),
            $hash
        )) {
            abort(403, 'Link verifikasi email tidak valid.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new \Illuminate\Auth\Events\Verified($user));
        }

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard', ['verified' => 1])
            ->with('success', 'Email berhasil diverifikasi. Selamat datang di Orbii!');
    }
}