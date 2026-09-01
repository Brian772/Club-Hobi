<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Appeal;

class AppealController extends Controller
{
    public function create()
    {
        return view('appeals.index', [
            'appeals' => Appeal::where('user_id', auth()->id())->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['reason' => 'required|string|max:1000']);

        abort_if(
            Appeal::where('user_id', auth()->id())->where('status', 'pending')->exists(),
            409, 'You already have a pending appeal. Please wait for it to be reviewed before submitting another'
        );

        Appeal::create([
            'id' => Str::uuid(),
            'user_id' => auth()->id(),
            'reason' => $request->input('reason'),
        ]);

        return back()->with('success', 'Your appeal has been submitted.');
    }
}
