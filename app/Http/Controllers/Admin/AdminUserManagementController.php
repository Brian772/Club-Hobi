<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserManagementController extends Controller
{
    public function index()
    {
        // $users = User::paginate(20);

        // $activeUsers = User::where('status', 'active')->paginate(20);

        // $suspendedUsers = User::where('status', 'suspended')->paginate(20);

        // $bannedUsers = User::where('status', 'banned')->paginate(20);
        // return view('admin.user-management', compact('users', 'activeUsers', 'suspendedUsers', 'bannedUsers'));
        return view('admin.user-management');
    }
}
