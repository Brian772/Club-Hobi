<?php

namespace App\Http\Controllers\Clubs;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Post;
use App\Models\Hobby;
use App\Models\ClubJoinRequest;
use App\Models\ClubMember;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $user = Auth::user();

        $allClubs = Club::withcount('members')->get();

        $userInterest = array_map('trim', $user->interest_array);

        $userInterestId = $userInterest ? Hobby::whereIn('name', $userInterest)->pluck('id') : collect();

        $pendingRequests = ClubJoinRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->get()
            ->keyBy('club_id');

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

        return view('clubs.index', compact('recomendedClubs', 'allClubs', 'isEmpty', 'joinedClub', 'pendingRequests'));
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

        $creator = ClubMember::with('user')
            ->where('club_id', $id)
            ->where('role', 'owner')
            ->first();

        return view('clubs.show', compact('club', 'isJoined', 'posts', 'members', 'user', 'creator'));
    }

    public function settings($id) {
        
        $club = Club::findOrFail($id);

        $this->authorize('view', $club);

        $members = ClubMember::with('user')
            ->orderByRaw('user_id = ? DESC', [Auth::user()->id])
            ->where('club_id', $id)
            ->paginate(15);

            $moderator = ClubMember::with('user')
            ->where('club_id', $id)
            ->where('role', 'moderator')
            ->get();

        $joinRequests = ClubJoinRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->where('club_id', $id)
            ->where('status', 'pending')
            ->withCount('user')
            ->get();

        return view('clubs.settings', compact('club', 'members', 'moderator', 'joinRequests'));
    }

    public function update(Request $request, Club $club)
    {
        if (Gate::denies('update', $club)) {
            return redirect()->route('clubs.show', ['club' => $club->id])->with('error', 'Anda tidak memiliki izin untuk mengedit klub ini.');
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

    public function leave(Request $request, $id)
    {
        $userId = Auth::id();

        ClubMember::where('club_id', $id)
            ->where('user_id', $userId)
            ->delete();

        return redirect()->route('clubs.index')->with('success', 'berhasil keluar dari klub!');
    }

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

    public function promoteModerator(Club $club, $id)
    {
        if (Gate::denies('isOwner', $club)) {
            return redirect()->route('clubs.show', ['club' => $club->id])->with('warning', 'Anda tidak memiliki izin untuk melakukan hal ini.');
        }

        ClubMember::where('club_id', $club->id)
            ->where('user_id', $id)
            ->update(['role' => 'moderator']);

        return redirect()->route('clubs.settings', ['club' => $club->id])->with('success', 'Moderator berhasil diperbarui!');
    
        }
    public function demoteModerator(Club $club, $id)
    {
        if (Gate::denies('isOwner', $club)) {
            return redirect()->route('clubs.show', ['club' => $club->id])->with('warning', 'Anda tidak memiliki izin untuk melakukan hal ini.');
        }

        ClubMember::where('club_id', $club->id)
            ->where('user_id', $id)
            ->update(['role' => 'member']);

        return redirect()->route('clubs.settings', ['club' => $club->id])->with('success', 'Moderator berhasil diperbarui!');
    }

    public function deleteClub(Club $club)
    {
        if (Gate::denies('isOwner', $club)) {
            return redirect()->route('clubs.show', ['club' => $club->id])->with('warning', 'Anda tidak memiliki izin untuk melakukan hal ini.');
        }

        $club->delete();

        return redirect()->route('clubs.index')->with('success', 'Club berhasil dihapus!');
    }
}
