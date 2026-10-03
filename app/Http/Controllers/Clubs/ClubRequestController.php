<?php

namespace App\Http\Controllers\Clubs;

use App\Http\Requests\StoreClubRequestRequest;
use App\Http\Controllers\Controller;
use App\Models\ClubRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    public function rules(): array
    {
        return [
            'cover' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'hobby_id' => ['required', 'exists:hobbies,id'],
            'privacy_club' => ['required', 'in:public,private'],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'cover.required' => 'Gambar cover harus dipilih.',
            'cover.image' => 'File harus berupa gambar.',
            'cover.mimes' => 'Format gambar harus berupa jpeg atau png.',
            'cover.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
            'name.required' => 'Nama klub harus diisi.',
            'name.string' => 'Nama klub harus berupa teks.',
            'name.max' => 'Nama klub tidak boleh lebih dari 255 karakter.',
            'name.min' => 'Nama klub harus lebih dari 5 karakter.',
            'description.string' => 'Deskripsi klub harus berupa teks.',
            'description.max' => 'Deskripsi klub tidak boleh lebih dari 1000 karakter.',
            'hobby_id.required' => 'Hobi harus dipilih.',
            'hobby_id.exists' => 'Hobi yang dipilih tidak valid.',
            'privacy_club.required' => 'Privasi klub harus dipilih.',
            'privacy_club.in' => 'Privasi klub harus berupa public atau private.',
            'reason.required' => 'Alasan pengajuan harus diisi.',
            'reason.string' => 'Alasan pengajuan harus berupa teks.',
            'reason.max' => 'Alasan pengajuan tidak boleh lebih dari 255 karakter.',
            'reason.min' => 'Alasan pengajuan harus lebih dari 5 karakter.',
        ];
    }

    public function storeRequest(StoreClubRequestRequest $request)
    {
        $validated = $request->validated();
        $coverPath = null;
        DB::beginTransaction();

        try {
            if ($request->hasFile('cover')) {
                $coverPath = $request->file('cover')->store('club/covers', 'public');
            }

            ClubRequest::create([
                'id'            => Str::uuid(),
                'user_id'       => Auth::id(),
                'name'          => $validated['name'],
                'description'   => $validated['description'] ?? null,
                'hobby_id'      => $validated['hobby_id'],
                'privacy_club'  => $validated['privacy_club'] ?? 'public',
                'reason'        => $validated['reason'],
                'cover_url'     => $coverPath,
                'status'        => 'pending',
            ]);

            DB::commit();

            return redirect()->route('clubs.index')->with('success', 'Permintaan klub berhasil dikirim!');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }

            return redirect()->route('clubs.index')->with('error', 'Permintaan klub gagal dikirim!');
        }
    }

    public function destroylRequest($id)
    {
        $clubRequest = ClubRequest::findOrFail($id);

        if ($clubRequest->status !== 'pending') {
            return redirect()->route('clubs.request.list')->with('error', 'Hanya permintaan klub yang berstatus pending yang dapat dibatalkan.');
        }

        $clubRequest->delete();

        return redirect()->route('clubs.request.list')->with('success', 'Permintaan klub berhasil dibatalkan.');
    }
}
