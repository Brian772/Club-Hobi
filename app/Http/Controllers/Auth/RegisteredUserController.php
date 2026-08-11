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

        // Kalau proses registrasi sudah selesai (sudah login), lempar ke dashboard.
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Step 2 butuh data step 1 (email/password) yang tersimpan di session.
        if ($step === 2 && !session()->has('register.email')) {
            return redirect()->route('register', ['step' => 1]);
        }

        // Step 3 butuh data step 1 & step 2 yang tersimpan di session.
        // Catatan: bukan lagi Auth::check(), karena user BARU dibuat & login
        // di step 3 -- sebelum itu belum ada akun sama sekali di database.
        if ($step === 3 && (!session()->has('register.email') || !session()->has('register.name'))) {
            return redirect()->route('register', ['step' => 1]);
        }

        // $categories hanya dibutuhkan untuk pilihan hobi di step 3, jadi
        // query-nya dibatasi supaya step 1 & 2 tidak query ke database sia-sia.
        // Diambil langsung dari tabel `clubs` (kolom `category`), tanpa model.
        $categories = $step === 3
            ? DB::table('clubs')->whereNotNull('category')->distinct()->pluck('category')
            : collect();

        return view('auth.register', compact('step', 'categories'));
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
     * Step 2: Simpan Nama, Bio & Avatar sementara ke Session.
     *
     * PENTING: belum membuat record User di database. Avatar tetap perlu
     * diupload ke disk sekarang (karena tidak bisa disimpan di session),
     * tapi path-nya baru "dipakai" saat user benar-benar dibuat di step 3.
     * Kalau user tidak menyelesaikan step 3, record User memang belum ada.
     */
    public function step2(Request $request): RedirectResponse
    {
        if (!session()->has('register.email')) {
            return redirect()->route('register', ['step' => 1]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar_url' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        if ($request->hasFile('avatar_url')) {
            session(['register.avatar_url' => $request->file('avatar_url')->store('avatars', 'public')]);
        }

        session([
            'register.name' => $validated['name'],
            'register.bio' => $validated['bio'] ?? null,
        ]);

        // "Skip For Now" mengirim request ke route yang sama (avatar_url
        // dikosongkan lewat JS di blade), jadi otomatis lanjut ke step 3.
        return redirect()->route('register', ['step' => 3]);
    }

    /**
     * Step 3: Simpan Hobi, BARU membuat User di database, lalu login.
     *
     * Ini titik commit tunggal: kalau user drop di step 1 atau step 2,
     * tidak ada apa pun yang tersimpan di tabel users.
     */
    public function step3(Request $request): RedirectResponse
    {
        if (!session()->has('register.email') || !session()->has('register.name')) {
            return redirect()->route('register', ['step' => 1]);
        }

        $request->validate([
            'hobbies' => ['required', 'array', 'min:1'],
            'hobbies.*' => ['string', 'exists:clubs,category'],
        ]);

        $user = User::create([
            'name' => session('register.name'),
            'bio' => session('register.bio'),
            'avatar_url' => session('register.avatar_url'),
            'email' => session('register.email'),
            'password_hash' => session('register.password'),
        ]);

        event(new Registered($user));

        session(['user_hobbies' => $request->input('hobbies')]);

        Auth::login($user);

        $request->session()->regenerate();
        session()->forget('register');

        return redirect()->route('dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name . '!');
    }
}