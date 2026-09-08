<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClubRequest;
use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminClubRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $clubRequest = ClubRequest::with('requester', 'hobby')->get();
        return view("admin.clubs.request", compact("clubRequest", "user"));
    }

    public function show(ClubRequest $clubRequest)
    {
        $user = Auth::user();
        return view("admin.clubs.request-show", compact("clubRequest", "user"));
    }

    public function accept(ClubRequest $clubRequest)
    {
        $clubRequest = ClubRequest::findOrFail($clubRequest->id);
        if ($clubRequest->status !== 'pending') {
            return redirect()->route('admin.clubs.request')->with('error', 'Request already processed or an error occurred while processing the request.');
        }
        DB::beginTransaction();

        try {
            $club = Club::create([
                'id' => Str::uuid(),
                'name' => $clubRequest->name,
                'hobby_id' => $clubRequest->hobby_id,
                'description' => $clubRequest->description,
                'created_by' => $clubRequest->user_id,
                'cover_url' => $clubRequest->cover_url,
                'created_at' => now(),
            ]);

            ClubMember::create([
                'id' => Str::uuid(),
                'club_id' => $club->id,
                'user_id' => $clubRequest->user_id,
                'role' => 'owner',
                'joined_at' => now(),
            ]);

            $clubRequest->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            DB::commit();
        } catch(\Throwable $e){
            DB::rollBack();
            return redirect()->route('admin.clubs.request')->with('error', 'Failed to accept club request.');
        }

        return redirect()->route('admin.clubs.request')->with('success', 'Club request accepted successfully.');
    }

    public function reject(ClubRequest $clubRequest)
    {
        $clubRequest = ClubRequest::findOrFail($clubRequest->id);
        if ($clubRequest->status !== 'pending') {
            return redirect()->route('admin.clubs.request')->with('error', 'Request already processed or an error occurred while processing the request.');
        }
        DB::beginTransaction();

        try {
            $clubRequest->update([
                'status' => 'rejected',
                'rejected_reason' => request('reason'),
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            DB::commit();
        } catch(\Throwable $e){
            DB::rollBack();
            return redirect()->route('admin.clubs.request')->with('error', 'Failed to reject club request.');
        }

        return redirect()->route('admin.clubs.request')->with('success', 'Club request rejected successfully.');
    }
}
