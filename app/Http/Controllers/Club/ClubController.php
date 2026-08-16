<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::withCount('members')->get();

        return view('clubs.index', compact('clubs'));
    }

    public function show($id)
    {
        $club = Club::withCount('members')
            ->with(['posts.user', 'files'])
            ->findOrFail($id);

        $isJoined = ClubMember::where('club_id', $id)
            ->where('user_id', Auth::user()->id)
            ->exists();

        return view('clubs.show', compact('club', 'isJoined'));
    }

    public function join(Request $request, $id)
    {
        $userId = Auth::id();

        $alreadyJoined = ClubMember::where('club_id', $id)
            ->where('user_id', $userId)
            ->exists();

        if(!$alreadyJoined) {
            ClubMember::create([
                'club_id' => $id,
                'user_id' => $userId,
                'joined_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'berhasil bergabung dengan klub!');
    }

    public function leave(Request $request, $id)
    {
        $userId = Auth::id();

        ClubMember::where('club_id', $id)
            ->where('user_id', $userId)
            ->delete();

        return redirect()->back()->with('success', 'berhasil keluar dari klub!');
    }

    public function kickMember(Request $request, $clubId, $userId)
    {
        if(Auth::user()->role_global !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        ClubMember::where('club_id', $clubId)
            ->where('user_id', $userId)
            ->delete();

        return redirect()->back()->with('success', 'Anggota berhasil dikeluarkan dari klub!');
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->role_global !== 'admin') {
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
