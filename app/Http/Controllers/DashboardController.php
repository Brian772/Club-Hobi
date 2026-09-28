<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return $this->renderDashboard('home');
    }

    public function profile()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return $this->renderDashboard('profile');
    }

    public function posts()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return $this->renderDashboard('posts');
    }

    public function clubFiles()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return $this->renderDashboard('club_files');
    }

    private function renderDashboard(string $activeMenu = 'home')
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User|null $user */
        $user = Auth::user();
        $userId = Auth::id();

        // 1. Ambil list ID klub yang diikuti user
        $userClubIds = ClubMember::where('user_id', $userId)->pluck('club_id');

        // 2. Ambil data klub untuk widget/sidebar
        $joinedClub = Club::query()
            ->whereIn('id', $userClubIds->take(3))
            ->withCount('members')
            ->get();

        // 3. Tarik postingan dari klub yang diikuti user
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

        // Fallback jika belum ada postingan dari klub yang diikuti
        if ($feedPosts->isEmpty()) {
            $feedPosts = Post::query()
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
        }

        return view('dashboard', compact('user', 'joinedClub', 'feedPosts', 'activeMenu'));
    }
}