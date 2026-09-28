<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Appeal;
use Illuminate\Support\Facades\AUTH;

class AppealController extends Controller
{
    public function index()
    {
        $alreadyAppealed = Appeal::where('user_id', AUTH::user()->id)->where('status', 'pending')->exists();
        $appeal = Appeal::where('user_id', AUTH::user()->id)->where('status', 'pending')->first();
        $user = AUTH::user();

        return view('appeals.index', compact('user', 'alreadyAppealed', 'appeal'));
    }

    public function store(Request $request)
    {
        $request->validate(['reason' => 'required|string|max:1000']);

        abort_if(
            Appeal::where('user_id', AUTH::user()->id)->where('status', 'pending')->exists(),
            409,
            'You already have a pending appeal. Please wait for it to be reviewed before submitting another'
        );

        Appeal::create([
            'id' => Str::uuid(),
            'user_id' => AUTH::user()->id,
            'reason' => $request->input('reason'),
        ]);

        return back()->with('success', 'Your appeal has been submitted.');
    }
}
