<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Appeal;
use App\Models\AuditLog;
use Illuminate\Support\Facades\AUTH;

class AppealController extends Controller
{
    public function index()
    {
        $user = AUTH::user();

        $alreadyAppealed = Appeal::where('user_id', AUTH::user()->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', $user->status_updated_at)
            ->exists();

        $appeal = Appeal::where('user_id', AUTH::user()->id)
            ->where('status', 'pending')->latest()
            ->where('created_at', '>=', $user->status_updated_at)
            ->first();

        $rejectedAppeal = Appeal::where('user_id', AUTH::user()->id)
            ->where('status', 'rejected')
            ->where('created_at', '>=', $user->status_updated_at)
            ->latest()
            ->first();

        return view('appeals.index', compact('user', 'alreadyAppealed', 'appeal', 'rejectedAppeal'));
    }

    public function store(Request $request, Appeal $appeal)
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

        AuditLog::create([
            'id' => Str::uuid(),
            'user_id' => Auth::id(),
            'action' => 'Appeal Request',
            'target_type' => 'Appeal',
            'target_id' => $appeal->id,
            'metadata' => [
                'reason' => $request->input('reason'),
            ],
        ]);

        return back()->with('success', 'Your appeal has been submitted.');
    }
}
