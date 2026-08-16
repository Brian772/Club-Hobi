<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        
        return view('dashboard', [
            'activeMenu' => 'dashboard',
            compact('user'),
        ]);
    }

    public function profile(): View
    {
        return view('dashboard', [
            'activeMenu' => 'profile'
        ]);
    }

    public function posts(): View
    {
        return view('dashboard', [
            'activeMenu' => 'posts'
        ]);
    }

    public function clubFiles(): View
    {
        return view('dashboard', [
            'activeMenu' => 'club_files'
        ]);
    }
}