<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use App\Models\Hobby;
use Illuminate\Http\Request;

class AdminClubManagementController extends Controller
{
    public function index()
    {
        $clubs = Club::withCount('members')->get();
        $hobbies = Hobby::all();

        return view('admin.club-management.index', compact('clubs', 'hobbies'));
    }

    public function show(Club $club)
    {
        $clubs = Club::with('creator', 'hobby')->where('id', $club->id)->first();

        return view('admin.club-management.show', compact('clubs'));
    }
}
