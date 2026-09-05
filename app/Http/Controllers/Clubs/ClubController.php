<?php

namespace App\Http\Controllers\Clubs;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Post;
use App\Models\Hobby;
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

        $userInterestId = $userInterest ? Hobby::whereIn('name', $userInterest)->pluck('id') : collect();

        $joinedClub = Club::withCount('members')
            ->whereHas('members', fn($q) => $q->where('user_id', $user->id))
            ->get();

        $joinedClubIds = $joinedClub->pluck('id');

        $recomendedClubs = $userInterestId->isNotEmpty()
            ? Club::with('hobby')
            ->withCount('members')
            ->whereIn('hobby_id', $userInterestId)
            ->whereNotIn('id', $joinedClubIds)
            ->get()
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

        $clubsIds = $user->clubs->pluck('id');

        $posts = Post::query()
            ->with(['user', 'club', 'author', 'comments.user'])
            ->orderByDesc('is_announcement')
            ->whereIn('club_id', $clubsIds)
            ->withCount(['comments', 'likes'])
            ->latest()
            ->get();

        $members = ClubMember::with('user')
            ->orderByRaw('user_id = ? DESC', [Auth::user()->id])
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

