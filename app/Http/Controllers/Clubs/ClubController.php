<?php

namespace App\Http\Controllers\Clubs;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Post;
use App\Models\Hobby;
use App\Models\ClubActivity;
use App\Models\AuditLog;
use App\Models\ClubJoinRequest;
use App\Models\ClubMember;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        $userClubIds = ClubMember::where('user_id', $user->id)->pluck('club_id');

        $posts = Post::query()
            ->where('club_id', $id)
            ->with([
                'author',
                'club',
                'media',
                'comments' => function ($query) {
                    $query->with('user')->oldest();
                },
                'likes'
            ])
            ->withCount(['comments', 'likes'])
            ->latest()
            ->take(20)
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

    public function settings(Request $request, $id)
    {

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

            $activities = ClubActivity::query()
            ->with('user')
            ->where('club_id', $id)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                        ->orWhere('target_type', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('clubs.settings', compact('club', 'members', 'moderator', 'joinRequests', 'activities'));
    }

    public function showActivity($clubId, $activityId)
    {
        $club = Club::findOrFail($clubId);
        $activity = ClubActivity::with('user')->findOrFail($activityId);
    
        $this->authorize('view', $club);

        return view('clubs.show-activity', compact('club', 'activity'));
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

        DB::beginTransaction();

        try {
            if ($request->hasFile('cover')) {
                if ($club->cover_url && Storage::disk('public')->exists($club->cover_url)) {
                    Storage::disk('public')->delete($club->cover_url);
                }

                $path = $request->file('cover')->store('club/covers', 'public');
                $validated['cover_url'] = $path;
            }

            $club->update($validated);

            ClubActivity::create([
                'id' => Str::uuid(),
                'actor_id' => Auth::id(),
                'club_id' => $club->id,
                'action' => 'Update Club',
                'target_type' => 'Club',
                'target_id' => $club->id,
                'metadata' => [
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                    'cover_url' => $validated['cover_url'] ?? null,
                ],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while updating the club.');
        }

        return redirect()->back()->with('success', 'Klub berhasil diperbarui!');
    }

    public function leave(Request $request, $id)
    {
        $userId = Auth::id();

        DB::beginTransaction();

        try {
            ClubActivity::create([
                'id' => Str::uuid(),
                'actor_id' => Auth::id(),
                'club_id' => $id,
                'action' => 'Leave Club',
                'target_type' => 'User',
                'target_id' => $userId,
                'metadata' => [
                    'role' => ClubMember::where('club_id', $id)->where('user_id', $userId)->value('role'),
                ],
            ]);

            ClubMember::where('club_id', $id)
                ->where('user_id', $userId)
                ->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while leaving the club.');
        }

        return redirect()->route('clubs.index')->with('success', 'berhasil keluar dari klub!');
    }

    public function kickMember(Request $request, $clubId, $userId)
    {
        if (Auth::user()->role_global !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        DB::beginTransaction();

        try {
            ClubActivity::create([
                'id' => Str::uuid(),
                'actor_id' => Auth::id(),
                'club_id' => $clubId,
                'action' => 'Kick Member',
                'target_type' => 'User',
                'target_id' => $userId,
                'metadata' => [
                    'kicked_by' => Auth::id(),
                    'role' => ClubMember::where('club_id', $clubId)->where('user_id', $userId)->value('role'),
                ],
            ]);

            ClubMember::where('club_id', $clubId)
                ->where('user_id', $userId)
                ->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while kicking the member.');
        }


        return redirect()->back()->with('success', 'Anggota berhasil dikeluarkan dari klub!');
    }

    public function promoteModerator(Club $club, $id)
    {
        if (Gate::denies('isOwner', $club)) {
            return redirect()->route('clubs.show', ['club' => $club->id])->with('warning', 'Anda tidak memiliki izin untuk melakukan hal ini.');
        }

        DB::beginTransaction();

        try {
            ClubMember::where('club_id', $club->id)
                ->where('user_id', $id)
                ->update(['role' => 'moderator']);

            ClubActivity::create([
                'id' => Str::uuid(),
                'actor_id' => Auth::id(),
                'club_id' => $club->id,
                'action' => 'Promote Moderator',
                'target_type' => 'User',
                'target_id' => $id,
                'metadata' => [
                    'promoted_by' => Auth::user()->name,
                    'previous_role' => 'member',
                    'new_role' => 'moderator',
                ],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('clubs.settings', ['club' => $club->id])->with('error', 'An error occurred while promoting moderator.');
        }

        return redirect()->route('clubs.settings', ['club' => $club->id])->with('success', 'Moderator berhasil diperbarui!');
    }
    public function demoteModerator(Club $club, $id)
    {
        if (Gate::denies('isOwner', $club)) {
            return redirect()->route('clubs.show', ['club' => $club->id])->with('warning', 'Anda tidak memiliki izin untuk melakukan hal ini.');
        }

        DB::beginTransaction();

        try {
            ClubMember::where('club_id', $club->id)
                ->where('user_id', $id)
                ->update(['role' => 'member']);

            ClubActivity::create([
                'id' => Str::uuid(),
                'actor_id' => Auth::id(),
                'club_id' => $club->id,
                'action' => 'Demote Moderator',
                'target_type' => 'User',
                'target_id' => $id,
                'metadata' => [
                    'demoted_by' => Auth::user()->name,
                    'previous_role' => 'moderator',
                    'new_role' => 'member',
                ],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('clubs.settings', ['club' => $club->id])->with('error', 'An error occurred while demoting moderator.');
        }

        return redirect()->route('clubs.settings', ['club' => $club->id])->with('success', 'Moderator berhasil diperbarui!');
    }

    public function deleteClub(Club $club)
    {
        if (Gate::denies('isOwner', $club)) {
            return redirect()->route('clubs.show', ['club' => $club->id])->with('warning', 'Anda tidak memiliki izin untuk melakukan hal ini.');
        }

        AuditLog::create([
            'id' => Str::uuid(),
            'user_id' => Auth::id(),
            'action' => 'Delete Club',
            'target_type' => 'Club',
            'target_id' => $club->id,
            'metadata' => [
                'club_name' => $club->name,
            ],
        ]);

        $club->delete();

        return redirect()->route('clubs.index')->with('success', 'Club berhasil dihapus!');
    }
}
