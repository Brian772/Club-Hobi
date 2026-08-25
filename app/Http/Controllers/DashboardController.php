<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $joinedClub = ClubMember::where('user_id', $user->id)->pluck('club_id')
            ->take(3);

        $joinedClub = Club::whereIn('id', $joinedClub)->withCount('members')->get();

        $clubsIds = $user->clubs->pluck('id');

        $feedPosts = Post::query()
            ->with(['user', 'club'])
            ->orderByDesc('is_announcement')
            ->whereIn('club_id', $clubsIds)
            ->latest()
            ->get();
        
        return view('dashboard', compact('user', 'joinedClub', 'feedPosts'));
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