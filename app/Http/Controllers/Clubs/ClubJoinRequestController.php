<?php

namespace App\Http\Controllers\Clubs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Club;
use App\Models\ClubActivity;
use App\Models\ClubMember;
use App\Models\ClubJoinRequest;

class ClubJoinRequestController extends Controller
{

    public function storeRequest(Club $club)
    {
        $user_id = Auth::user()->id;
        $club_id = $club->id;

        DB::beginTransaction();

        try {
            $existRequest = ClubJoinRequest::where('club_id', $club_id)
                ->where('user_id', $user_id)
                ->where('status', 'pending')
                ->first();

            if ($existRequest) {
                return redirect()->back()->with('warning', 'You have already requested to join this club.');
            }

            ClubJoinRequest::create([
                'id' => Str::uuid(),
                'club_id' => $club_id,
                'user_id' => $user_id,
                'status' => 'pending',
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while sending the join request.');
        }

        return redirect()->back()->with('success', 'Join request sent successfully.');
    }

    public function acceptRequest(Club $club, $id)
    {
        $joinRequest = ClubJoinRequest::where('user_id', $id)
            ->where('club_id', $club->id)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($joinRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Request already processed or an error occurred while processing the request.');
        }
        DB::beginTransaction();

        try {
            ClubMember::create([
                'id' => Str::uuid(),
                'club_id' => $joinRequest->club_id,
                'user_id' => $joinRequest->user_id,
                'role' => 'member',
                'joined_at' => now(),
            ]);

            ClubActivity::create([
                'id' => Str::uuid(),
                'actor_id' => Auth::id(),
                'club_id' => $club->id,
                'action' => 'Accept Join Request',
                'target_type' => 'User',
                'target_id' => $joinRequest->user_id,
                'metadata' => [
                    'role' => $joinRequest->user->role_global ?? 'member',
                ],
            ]);

            $joinRequest->update([
                'status' => 'approved',
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Join request approved successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while processing the join request.');
        }
    }

    public function rejectRequest(Club $club, $id)
    {
        $joinRequest = ClubJoinRequest::where('user_id', $id)
            ->where('club_id', $club->id)
            ->where('status', 'pending')
            ->first();

        if ($joinRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Request already processed or an error occurred while processing the request.');
        }
        DB::beginTransaction();
        try {
            $joinRequest->update([
                'status' => 'rejected',
            ]);

            ClubActivity::create([
                'id' => Str::uuid(),
                'actor_id' => Auth::id(),
                'club_id' => $club->id,
                'action' => 'Reject Join Request',
                'target_type' => 'User',
                'target_id' => $joinRequest->user_id,
                'metadata' => [
                    'role' => $joinRequest->user->role_global ?? 'member',
                ],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while processing the join request.');
        }

        return redirect()->back()->with('success', 'Join request rejected successfully.');
    }

    public function cancelRequest(Club $club)
    {
        $pendingRequests = ClubJoinRequest::where('club_id', $club->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        if ($pendingRequests->status !== 'pending') {
            return redirect()->back()->with('error', 'Request already processed or an error occurred while processing the request.');
        }
        DB::beginTransaction();
        try {
            $pendingRequests->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while canceling the join request.');
        }
        return redirect()->back()->with('success', 'Join request canceled successfully.');
    }
}
