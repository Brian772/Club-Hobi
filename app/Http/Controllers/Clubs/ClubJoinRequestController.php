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
use App\Models\Notification;

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

            Notification::createForUser(
                $club->created_by,
                'Permintaan Bergabung Klub',
                Auth::user()->name . ' meminta bergabung ke klub ' . $club->name,
                'other',
                $user_id,
            );

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

            Notification::createForUser(
                $joinRequest->user_id,
                'Permintaan Bergabung Klub Diterima',
                'Permintaan bergabung ke klub ' . $club->name . ' telah diterima.',
                'other',
                $joinRequest->id
            );

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

            Notification::createForUser(
                $joinRequest->user_id,
                'Permintaan Bergabung Klub Ditolak',
                'Permintaan bergabung ke klub ' . $club->name . ' telah ditolak.',
                'other',
                $joinRequest->id
            );

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

    public function join(Club $club)
    {
        $user_id = Auth::user()->id;
        $club_id = $club->id;

        if ($club->is_required_request === 'true') {
            return redirect()->back()->with('error', 'This club requires a join request. You cannot join directly.');
        }

        DB::beginTransaction();

        try {
            $existMember = ClubMember::where('club_id', $club_id)
                ->where('user_id', $user_id)
                ->first();

            if ($existMember) {
                return redirect()->back()->with('warning', 'You are already a member of this club.');
            }

            ClubMember::create([
                'id' => Str::uuid(),
                'club_id' => $club_id,
                'user_id' => $user_id,
                'role' => 'member',
                'joined_at' => now(),
            ]);

            ClubActivity::create([
                'id' => Str::uuid(),
                'actor_id' => Auth::id(),
                'club_id' => $club->id,
                'action' => 'Join Club',
                'target_type' => 'User',
                'target_id' => Auth::id(),
            ]);

            Notification::createForUser(
                $club->created_by,
                'Anggota Baru Bergabung',
                Auth::user()->name . ' telah bergabung ke klub ' . $club->name,
                'other',
                $user_id,
            );

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while joining the club.');
        }

        return redirect()->back()->with('success', 'Successfully joined the club.');
    }
}
