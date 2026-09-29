<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Club;
use App\Models\AuditLog;
use App\Models\ClubRequest;
use App\Models\User;
use App\Models\Hobby;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminOverviewController extends Controller
{
    public function index()
    {
        $user = User::get();
        $userCount = User::count();
        $joinedUsers = User::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $userActiveCount = User::where('status', 'active')->count();
        $clubCount = Club::count();
        $pendingClubCount = ClubRequest::where('status', 'pending')->count();
        $reports = Report::where('status', 'pending')->count();
        $hobbies = Hobby::all();

        $recentActivity = AuditLog::latest()->take(10)->get();
        return view('admin.overview', compact('user', 'userCount', 'clubCount', 'userActiveCount', 'joinedUsers', 'pendingClubCount', 'reports', 'recentActivity', 'hobbies'));
    }

    public function storeHobby(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $hobby = Hobby::create([
                'name' => $request->input('name'),
            ]);

            AuditLog::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'action' => 'Add Hobby',
                'target_type' => 'Hobby',
                'target_id' => $hobby->id,
                'metadata' => [
                    'name' => $request->input('name'),
                ],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.overview')->with('error', 'Failed to add hobby. Please try again later.');
        }

        return redirect()->route('admin.overview')->with('success', 'Hobby added successfully.');
    }

    public function deleteHobby($hobby)
    {
        $hobby = Hobby::findOrFail($hobby);

        if ($hobby->clubs()->count() > 0) {
            return redirect()->route('admin.overview')->with('error', 'Cannot delete hobby. It is associated with one or more clubs.');
        }

        DB::beginTransaction();

        try {
            AuditLog::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'action' => 'Delete Hobby',
                'target_type' => 'Hobby',
                'target_id' => $hobby->id,
                'metadata' => [
                    'name' => $hobby->name,
                ],
            ]);

            $hobby->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.overview')->with('error', 'Failed to delete hobby. Please try again later.');
        }


        return redirect()->route('admin.overview')->with('success', 'Hobby deleted successfully.');
    }
}
