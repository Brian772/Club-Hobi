<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Post;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $allClubs = Club::withcount('members')->get();

        $userInterest = array_map('trim', $user->interest_array);

        $joinedClub = Club::withCount('members')
            ->whereHas('members', fn($q) => $q->where('user_id', $user->id))
            ->get();

        $joinedClubIds = $joinedClub->pluck('id');

        if (!empty($userInterest)) {
            $recomendedClubs = Club::withCount('members')
                ->whereIn('category', $userInterest)
                ->get();
        }

        $recomendedClubs = $userInterest
            ? $allClubs->whereIn('category', $userInterest)->reject(fn($c) => $joinedClubIds->contains($c->id))->values()
            : collect();

        $isEmpty = $allClubs->isEmpty() && $recomendedClubs->isEmpty();

        return view('clubs.index', compact('recomendedClubs', 'allClubs', 'isEmpty', 'joinedClub'));
    }

    public function show($id)
    {
        $user = Auth::user();

        $club = Club::withCount('members')
            ->with(['posts.user', 'files'])
            ->findOrFail($id);

        $isJoined = ClubMember::where('club_id', $id)
            ->where('user_id', Auth::user()->id)
            ->exists();

        $posts = Post::with('user')
            ->orderByDesc('is_announcement')
            ->latest()
            ->where('club_id', $id)
            ->get();

        $members = ClubMember::with('user')
        ->where('club_id', $id)
        ->get();

        return view('clubs.show', compact('club', 'isJoined', 'posts', 'members', 'user'));
    }

    public function join(Request $request, $id)
    {
        $userId = Auth::id();

        $alreadyJoined = ClubMember::where('club_id', $id)
            ->where('user_id', $userId)
            ->exists();

        if (!$alreadyJoined) {
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

        return redirect()->route('clubs.index')->with('success', 'berhasil keluar dari klub!');
    }
}
