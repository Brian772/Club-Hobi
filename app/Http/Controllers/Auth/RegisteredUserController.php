<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman registrasi berdasarkan step.
     */
    public function create($step = 1): View|RedirectResponse
    {
        $step = (int) $step;

        if (!in_array($step, [1, 2, 3])) {
            abort(404);
        }

        // Step 3 hanya bisa diakses setelah login (dibuat otomatis di step 2).
        if ($step === 3) {
            if (!Auth::check()) {
                return redirect()->route('register', ['step' => 1]);
            }
        } elseif (Auth::check()) {
            return redirect()->route('profile.index');
        }

        return view('auth.register', compact('step'));
    }

    /**
     * Step 1: Simpan Email & Password sementara ke Session.
     */
    public function step1(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        session([
            'register.email' => $validated['email'],
            'register.password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('register', ['step' => 2]);
    }

    /**
     * Step 2: Simpan Data Pengguna ke Database & Buat Sesi Login.
     */
    public function step2(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar_url' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $avatarUrl = null;

        if ($request->hasFile('avatar_url')) {
            $avatarUrl = $request->file('avatar_url')->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'bio' => $validated['bio'],
            'avatar_url' => $avatarUrl,
            'email' => session('register.email'),
            'password_hash' => session('register.password'),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();
        session()->forget('register');

        return redirect()->route('register', ['step' => 3]);
    }

    /**
     * Step 3: Simpan Hobi Pengguna.
     */
    public function step3(Request $request): RedirectResponse
    {
        $request->validate([
            'hobbies' => ['required', 'array', 'min:1'],
            'hobbies.*' => ['string', 'exists:clubs,category'],
        ]);

        $selectedHobbies = $request->input('hobbies');

        session(['user_hobbies' => $selectedHobbies]);

        return redirect()->route('beranda')->with('success', 'Registrasi berhasil!');
    }
}