<?php

namespace App\Http\Controllers\Clubs;

use App\Http\Requests\StoreClubRequestRequest;
use App\Http\Controllers\Controller;
use App\Models\ClubRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ClubRequestController extends Controller
{
    public function request()
    {

        $hobbies = \App\Models\Hobby::all();
        return view('clubs.request', compact('hobbies'));
    }

    public function listRequest()
    {
        $user = Auth::user();
        $clubRequests = ClubRequest::where('user_id', $user->id)->get();

        return view('clubs.request-list', compact('clubRequests'));
    }

    public function detail($id)
    {
        $clubRequest = ClubRequest::findOrFail($id);

        return view('clubs.request-detail', compact('clubRequest'));
    }

    public function storeRequest(StoreClubRequestRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {

            $coverPath = null;
            if ($request->hasFile('cover')) {
                $coverPath = $request->file('cover')->store('club/covers', 'public');
            }
            ClubRequest::create([
                'id'         => Str::uuid(),
                'user_id'    => Auth::id(),
                'name'       => $validated['name'],
                'description'=> $validated['description'] ?? null,
                'hobby_id'   => $validated['hobby_id'],
                'reason'     => $validated['reason'],
                'cover_url'  => $coverPath,
                'status'     => 'pending',
            ]);
            DB::commit();

            return redirect()->route('clubs.index')->with('success', 'Permintaan klub berhasil dikirim!');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($coverPath ?? null) {
                Storage::disk('public')->delete($coverPath);
            }

            return redirect()->route('clubs.index')->with('error', 'Permintaan klub gagal dikirim!');
        }
    }
}
