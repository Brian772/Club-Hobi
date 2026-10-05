<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function heartbeat(Request $request)
    {
        User::whereKey($request->user()->id)->update(['last_seen_at' => now()]);

        return response()->noContent();
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['sometimes', 'array', 'max:100'],
            'ids.*' => ['uuid'],
        ]);

        $users = User::whereIn('id', $validated['ids'] ?? [])
            ->get(['id', 'last_seen_at'])
            ->mapWithKeys(fn (User $user) => [$user->id => ['online' => $user->isOnline()]]);

        return response()->json(['users' => $users]);
    }
}