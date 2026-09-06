<?php

namespace App\Http\Controllers\Clubs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\ClubJoinRequest;

class ClubJoinRequestController extends Controller
{

    public function storeRequest(Club $club)
    {
        $user_id = Auth::user()->id;
        $club_id = $club->id;

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
            return redirect()->back()->with('success', 'Join request sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'An error occurred while sending the join request.');
        }
    }

    public function acceptRequest(Club $club, $id)
    {
        $joinRequest = ClubJoinRequest::where('id', $id)
            ->where('club_id', $club->id)
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
        $joinRequest = ClubJoinRequest::where('id', $id)
            ->where('club_id', $club->id)
            ->firstOrFail();

        if ($joinRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Request already processed or an error occurred while processing the request.');
        }
        try {
            $joinRequest->update([
                'status' => 'rejected',
            ]);
            return redirect()->back()->with('success', 'Join request rejected successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'An error occurred while processing the join request.');
        }
    }

    public function cancelRequest(Club $club)
    {
        $pendingRequests = ClubJoinRequest::where('club_id', $club->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($pendingRequests->status !== 'pending') {
            return redirect()->back()->with('error', 'Request already processed or an error occurred while processing the request.');
        }
        try {
            $pendingRequests->delete();
            return redirect()->back()->with('success', 'Join request canceled successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'An error occurred while canceling the join request.');
        }
    }
}
