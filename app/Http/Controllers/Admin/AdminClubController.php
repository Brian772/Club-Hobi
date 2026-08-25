<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Post;
use App\Models\ClubMember;
use Illuminate\Http\Request;
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

    public function update(Request $request, $id)
    {
        if (Auth::user()->role_global !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        $request->validate([
            'description' => 'required|string',
            'cover_url' => 'nullable|string',
        ]);

        $club = Club::findorFail($id);
        $club->update([
            'description' => $request->description,
            'cover_url' => $request->cover_url ?? $club->cover_url,
        ]);

        return redirect()->back()->with('success', 'Klub berhasil diperbarui!');
    }
}
