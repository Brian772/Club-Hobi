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

        // 1. Ambil list ID klub yang diikuti user
        $userClubIds = ClubMember::where('user_id', $user->id)->pluck('club_id');

        // 2. Ambil data klub untuk widget/sidebar
        $joinedClub = Club::whereIn('id', $userClubIds->take(3))->withCount('members')->get();
        
        $feedPosts = Post::query()
            ->whereIn('club_id', $userClubIds)
            ->with([
                'author',
                'club',
                'media',
                'comments' => function ($query) {
                    $query->with('user')->oldest();
                },
                'likes'
            ])
            ->withCount(['comments', 'likes'])
            ->latest()
            ->take(20)
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