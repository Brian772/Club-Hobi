<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Hobby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    /**
     * Halaman utama Settings
     */
    public function settings()
    {
        $user = Auth::user();
        return view('settings.setting', [
            'user' => $user,
        ]);
    }

    /**
     * Halaman Profile
     */
    public function profilesettings()
    {
        $user = Auth::user();

        // Club yang sedang diikuti user
        $user->load('clubs');

        $interestNames = $user->interests
        ? array_filter(explode(',', $user->interests))
        : [];

        $interests = Hobby::whereIn('name', $interestNames)->orderBy('name')->get();

        $hobbies = Hobby::orderBy('name')->get();

        return view('settings.profilesettings', compact('user', 'hobbies', 'interests'));
    }

    /**
     * Update nama dan bio
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:150',
        ]);

        $user->name = $validated['name'];
        $user->bio = $validated['bio'] ?? null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil disimpan.'
        ]);
    }
    /**
     * Upload / ganti foto profile
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->avatar_url) {
            Storage::disk('public')->delete($user->avatar_url);
        }

        // Simpan foto baru ke storage/app/public/avatars
        $path = $request->file('avatar')->store('avatars', 'public');

        // Simpan path ke database
        $user->avatar_url = $path;
        $user->save();

        return redirect()
            ->route('settings.profile')
            ->with('success', 'Foto profile berhasil diperbarui.');
    }

    /**
     * Hapus foto profile
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar_url) {
            Storage::disk('public')->delete($user->avatar_url);

            $user->avatar_url = null;
            $user->save();
        }

        return redirect()
            ->route('settings.profile')
            ->with('success', 'Foto profile berhasil dihapus.');
    }

    /**
     * Tambah hobi / club
     */
    public function addHobby(Request $request)
    {
        $validated = $request->validate([
            'hobby_id' => ['required', 'exists:hobbies,id'],
        ]);

        $user = Auth::user();
        $hobby = Hobby::findOrFail($validated['hobby_id']);

        $current = $user->interests
        ? array_filter(explode(',', $user->interests))
        : [];

        if (!in_array($hobby->name, $current)) {
            $current[] = $hobby->name;
            $user->interests = implode(',', $current);
            $user->save();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hobi berhasil ditambahkan.',
                'hobby' => $hobby,
            ]);
        }

        return redirect()
            ->route('settings.profile')
            ->with('success', 'Hobi berhasil ditambahkan.');
    }

    /**
     * Hapus hobi / club dari profile user
     */
    public function deleteHobby(Request $request, $hobbyId)
    {
        $user = Auth::user();
        $hobby = Hobby::findOrFail($hobbyId);

        $current = $user->interests
        ? array_filter(explode(',', $user->interests))
        : [];

        $current = array_filter($current, fn ($name) => $name !== $hobby->name);

        $user->interests = implode(',', $current);
        $user->save();

        return response()->json(['success' => true]);;
    }

    /**
     * Halaman Account
     */
    public function accountsettings()
    {
        $user = Auth::user();

        return view('settings.accountsettings', compact('user'));
    }
}
