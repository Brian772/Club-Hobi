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
    public function settings()
    {
        $user = Auth::user();
        return view('settings.setting', [
            'user' => $user,
        ]);
    }

    public function profilesettings()
    {
        $user = Auth::user();
        $user->load('clubs');

        $interestNames = $user->interests
        ? array_filter(explode(',', $user->interests))
        : [];
        
        $clubs = Club::query()
            ->selectRaw('MIN(id) as id, category')
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        $interests = Hobby::whereIn('name', $interestNames)->orderBy('name')->get();

        $hobbies = Hobby::orderBy('name')->get();

        return view('settings.profilesettings', compact('user', 'hobbies', 'interests'));
    }

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

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();
        if ($user->avatar_url) {
            Storage::disk('public')->delete($user->avatar_url);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->avatar_url = $path;
        $user->save();

        return redirect()
            ->route('settings.profile')
            ->with('success', 'Foto profile berhasil diperbarui.');
    }

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
        $user->clubs()->syncWithoutDetaching([
            $request->club_id => [
                'id' => (string) Str::uuid(),
            ],
        ]);

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

    public function accountsettings()
    {
        $user = Auth::user();

        return view('settings.accountsettings', compact('user'));
    }
}