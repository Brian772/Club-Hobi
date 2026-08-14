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

        if ($step === 2 && !session()->has('register.email')) {
            return redirect()->route('register', ['step' => 1]);
        }

        if ($step === 3 && !Auth::check()) {
            return redirect()->route('register', ['step' => 1]);
        }

        if ($step === 1 && Auth::check()) {
            return redirect()->route('profile.index');
        }

        $categories = ['Music', 'Hiking', 'Fishing', 'Gaming', 'Football', 'Reading', 'Traveling', 'Swimming', 'Photography'];

        return view('auth.register', compact('step', 'categories'));
    }

    /**
     * Default register flow for classic Laravel tests.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'password_hash' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
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
        $skip = $request->boolean('skip');

        $rules = [
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar_url' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];

        if ($skip) {
            $rules['name'] = ['nullable', 'string', 'max:255'];
        } else {
            $rules['name'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        if ($skip && empty($validated['name'])) {
            $email = session('register.email', 'user@orbii.local');
            $defaultName = explode('@', $email)[0] ?? 'Orbii User';
            $defaultName = trim(preg_replace('/[._-]+/', ' ', $defaultName));
            $validated['name'] = $defaultName ? ucwords($defaultName) : 'Orbii User';
        }

        $avatarUrl = null;

        if ($request->hasFile('avatar_url')) {
            $avatarUrl = $request->file('avatar_url')->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
            'avatar_url' => $avatarUrl,
            'email' => session('register.email'),
            'password' => session('register.password'),
            'email_verified_at' => now(),
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
        $categories = ['Music', 'Hiking', 'Fishing', 'Gaming', 'Football', 'Reading', 'Traveling', 'Swimming', 'Photography'];

        $request->validate([
            'hobbies' => ['required', 'array', 'min:1'],
            'hobbies.*' => ['string', 'in:' . implode(',', $categories)],
        ]);

        $selectedHobbies = $request->input('hobbies');

        session(['user_hobbies' => $selectedHobbies]);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
    }
}