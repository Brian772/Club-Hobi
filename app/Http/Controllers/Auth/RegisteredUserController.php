<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
            return redirect()->route('dashboard');
        }

        $categories = [
            'Photography',
            'Fishing',
            'Reading',
            'Music',
            'Gaming',
            'Traveling',
            'Football',
            'Swimming',
            'Hiking',
        ];

        return view('auth.register', compact('step', 'categories'));
    }

    /**
     * Step 1: Simpan Email & Password sementara ke Session.
     */
    public function step1(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique(User::class, 'email'),
            ],
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
            session(['register.avatar_url' => $avatarUrl]);
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
            'hobbies' => ['nullable', 'array'],
            'hobbies.*' => ['nullable', 'string'],
        ]);

        $selectedHobbies = collect($request->input('hobbies', []))
            ->map(fn ($hobby) => trim((string) $hobby))
            ->filter()
            ->unique()
            ->values()
            ->all();

        session(['user_hobbies' => $selectedHobbies]);

        $user = Auth::user();

        if ($user) {
            $user->interests = implode(',', $selectedHobbies);
            $user->save();
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . ($user ? $user->name : '') . '!');
    }
}
