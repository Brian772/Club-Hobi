<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman registrasi berdasarkan step.
     */
    public function create($step = 1): View|RedirectResponse
    {
        $step = (int) $step;

        if (!in_array($step, [1, 2, 3], true)) {
            abort(404);
        }

        /*
         * User yang sudah login tidak perlu masuk
         * ke halaman registrasi lagi.
         */
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        /*
         * Step 2 membutuhkan email dan password
         * dari step 1.
         */
        if ($step === 2 && !session()->has('register.email')) {
            return redirect()->route('register', ['step' => 1]);
        }

        /*
         * Step 3 membutuhkan data dari step 1 dan 2.
         */
        if (
            $step === 3 &&
            (
                !session()->has('register.email') ||
                !session()->has('register.name')
            )
        ) {
            return redirect()->route('register', ['step' => 1]);
        }

        /*
         * Kategori hobby hanya diambil pada step 3.
         */
        $categories = $step === 3
            ? DB::table('clubs')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            : collect();

        return view(
            'auth.register',
            compact('step', 'categories')
        );
    }

    /**
     * STEP 1
     *
     * Simpan email dan password sementara
     * ke session.
     */
    public function step1(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'), // <--- Menolak email duplikat sejak awal
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        session([
            'register.email'    => $validated['email'],
            'register.password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('register', ['step' => 2]);
    }

    /**
     * STEP 2
     *
     * Simpan nama, bio, dan avatar sementara.
     */
    public function step2(Request $request): RedirectResponse
    {
        if (!session()->has('register.email')) {
            return redirect()->route('register', ['step' => 1]);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'bio' => [
                'nullable',
                'string',
                'max:500',
            ],
            'avatar_url' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        /*
         * Jika ada avatar baru, simpan ke storage.
         */
        if ($request->hasFile('avatar_url')) {
            $oldAvatar = session('register.avatar_url');

            if ($oldAvatar) {
                Storage::disk('public')->delete($oldAvatar);
            }

            $avatarPath = $request
                ->file('avatar_url')
                ->store('avatars', 'public');

            session([
                'register.avatar_url' => $avatarPath,
            ]);
        }

        session([
            'register.name' => $validated['name'],
            'register.bio' => $validated['bio'] ?? null,
        ]);

        return redirect()->route('register', ['step' => 3]);
    }

    /**
     * Selesaikan proses registrasi (Step 3).
     */
    public function step3(Request $request): RedirectResponse
    {
        $email = session('register.email');

        if (!$email) {
            return redirect()->route('register', ['step' => 1]);
        }

        // 1. Cek apakah user sudah terlanjur dibuat di database
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            Auth::login($existingUser);
            $request->session()->regenerate();
            $request->session()->forget('register');

            return redirect()
                ->route('dashboard')
                ->with('success', 'Selamat datang kembali, ' . $existingUser->name . '!');
        }

        // 2. Validasi pilihan hobi (dibuat opsional/nullable)
        $validated = $request->validate([
            'hobbies'   => ['nullable', 'array'],
            'hobbies.*' => ['string'],
        ]);

        // 3. Buat User baru ke database
        $user = User::create([
            'name'          => session('register.name'),
            'bio'           => session('register.bio'),
            'avatar_url'    => session('register.avatar_url'),
            'email'         => $email,
            'password_hash' => session('register.password'),
            'role_global'   => 'member',
            'status'        => 'active',
        ]);

        event(new Registered($user));

        if (!empty($validated['hobbies'])) {
            session(['user_hobbies' => $validated['hobbies']]);
        }

        // 4. Auto Login & Redirect ke Dashboard
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('register');

        return redirect()
            ->route('dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name . '!');
    }
}
