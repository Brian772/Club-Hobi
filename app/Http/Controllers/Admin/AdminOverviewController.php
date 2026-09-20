<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;
use App\Models\AuditLog;
use App\Models\ClubRequest;
use App\Models\User;
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

        $recentActivity = AuditLog::latest()->take(10)->get();
        return view('admin.overview', compact('user', 'userCount', 'clubCount', 'userActiveCount', 'joinedUsers', 'pendingClubCount', 'reports', 'recentActivity'));
    }
}
