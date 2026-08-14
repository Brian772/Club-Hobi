<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'activeMenu' => 'dashboard'
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