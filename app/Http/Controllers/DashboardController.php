<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Club;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $recommendedClubs = Club::query()
            ->withCount('members')
            ->take(3)
            ->get();

        $feedPosts = Post::query()
            ->with(['author', 'club'])
            ->latest()
            ->take(10)
            ->get();

        
        return view('dashboard', ['user'=> $user,
            'recommendedClubs' => $recommendedClubs,
            'feedPosts' => $feedPosts,
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