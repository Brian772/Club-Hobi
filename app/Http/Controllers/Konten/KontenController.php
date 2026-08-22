<?php

namespace App\Http\Controllers;

use App\Models\ClubFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KontenController extends Controller
{
    /**
     * Menampilkan semua file/dokumentasi.
     */
    public function index()
    {
        $files = ClubFile::with(['club', 'uploader'])
            ->latest('uploaded_at')
            ->get();

        return view('konten.index', compact('files'));
    }

    /**
     * Menampilkan form tambah file.
     */
    public function create()
    {
        return view('konten.create');
    }

    /**
     * Menyimpan file baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:20480',
            'type' => 'required|in:image,video,document',
        ]);

        $file = $request->file('file');

        $path = $file->store('club_files', 'public');

        ClubFile::create([
            'id' => Str::uuid(),
            'club_id' => $request->club_id,
            'uploaded_by' => auth()->id(),
            'title' => $request->title,
            'file_url' => $path,
            'type' => $request->type,
        ]);

        return redirect()
            ->route('konten.index')
            ->with('success', 'Dokumentasi berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail file.
     */
    public function show(string $id)
    {
        $file = ClubFile::with(['club', 'uploader'])
            ->findOrFail($id);

        return view('konten.show', compact('file'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(string $id)
    {
        $file = ClubFile::findOrFail($id);

        return view('konten.edit', compact('file'));
    }

    /**
     * Memperbarui data file.
     */
    public function update(Request $request, string $id)
    {
        $file = ClubFile::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:20480',
            'type' => 'required|in:image,video,document',
        ]);

        $data = [
            'title' => $request->title,
            'type' => $request->type,
        ];

        // Jika user mengganti file
        if ($request->hasFile('file')) {

            // Hapus file lama
            if ($file->file_url && Storage::disk('public')->exists($file->file_url)) {
                Storage::disk('public')->delete($file->file_url);
            }

            // Simpan file baru
            $data['file_url'] = $request->file('file')
                ->store('club_files', 'public');
        }

        $file->update($data);

        return redirect()
            ->route('konten.index')
            ->with('success', 'Dokumentasi berhasil diperbarui.');
    }

    /**
     * Menghapus file.
     */
    public function destroy(string $id)
    {
        $file = ClubFile::findOrFail($id);

        // Hapus file dari storage
        if ($file->file_url && Storage::disk('public')->exists($file->file_url)) {
            Storage::disk('public')->delete($file->file_url);
        }

        // Hapus data dari database
        $file->delete();

        return redirect()
            ->route('konten.index')
            ->with('success', 'Dokumentasi berhasil dihapus.');
    }
}