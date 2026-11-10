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
        $clubs = Club::with('creator', 'hobby', 'members')->where('id', $club->id)->first();

        return view('admin.club-management.show', compact('clubs'));
    }

    public function deleteClub(Request $request, Club $club)
    {
        DB::beginTransaction();

        try {
            $request->validateWithBag('deleteClub', [
                'confirm_name' => [
                    'required',
                    function ($attribute, $value, $fail) use ($club) {
                        if ($value !== $club->name) {
                            $fail('Nama club yang diketik tidak sesuai.');
                        }
                    },
                ],
            ], [
                'confirm_name.required' => 'Ketik nama club untuk konfirmasi.',
            ]);

            AuditLog::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'action' => 'Delete Club',
                'target_type' => 'Club',
                'target_id' => $club->id,
                'metadata' => [
                    'club_name' => $club->name,
                ],
            ]);

            ClubMember::where('club_id', $club->id)
                ->pluck('user_id')
                ->each(fn($memberId) => Notification::createForUser(
                    $memberId,
                    'Klub Dihapus',
                    'Klub ' . $club->name . ' telah dihapus oleh admin.',
                    'other',
                    $club->id
                ));

            $club->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['deleteClub' => 'An error occurred while deleting the club. Please try again.']);
        }
        return redirect()->route('admin.club-management')->with('success', 'Club deleted successfully.');
    }
}
