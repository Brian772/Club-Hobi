<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;
use Illuminate\Http\Request;

class AdminOverviewController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userCount = $user->count('id');
        $userActiveCount = $user->where('status', 'active')->count();
        $clubCount = Club::count();
        return view('admin.overview', compact('userCount', 'clubCount', 'userActiveCount'));
    }
}
