<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserManagementController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.user-management', compact('users'));
    }
}
