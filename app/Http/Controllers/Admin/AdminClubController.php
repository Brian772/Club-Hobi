<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Post;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdminClubController extends Controller
{
    public function kickMember(Request $request, $clubId, $userId)
    {
        if (Auth::user()->role_global !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        ClubMember::where('club_id', $clubId)
            ->where('user_id', $userId)
            ->delete();

        return redirect()->back()->with('success', 'Anggota berhasil dikeluarkan dari klub!');
    }

    public function edit($id)
    {
        $club = Club::findOrFail($id);
        return view('admin.clubs.edit', compact('club'));
    }

    public function update(Request $request,Club $club)
    {
        if (Auth::user()->role_global !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'cover_url' => 'nullable|image|mimes:jpeg,png|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            if ($club->cover_url && Storage::disk('public')->exists($club->cover_url)) {
                Storage::disk('public')->delete($club->cover_url);
            }

            $path = $request->file('cover')->store('club/covers', 'public');
            $validated['cover_url'] = $path;
        }

        $club->update($validated);

        return redirect()->route('clubs.show', $club->id)->with('success', 'Klub berhasil diperbarui!');
    }
}
