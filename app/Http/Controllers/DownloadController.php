<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    /**
     * Download file berdasarkan ID media.
     */
    public function download($mediaId)
    {
        $media = PostMedia::findOrFail($mediaId);

        $filePath = $this->resolveFilePath($media->file_path);

        if (!$filePath) {
            abort(404, 'File tidak ditemukan.');
        }

        return $this->downloadFile($filePath);
    }

    /**
     * Download file berdasarkan ID postingan.
     */
    public function downloadPostFile($postId)
    {
        $post = Post::findOrFail($postId);

        $filePath = $post->file_path ?? $post->media_url;

        $filePath = $this->resolveFilePath($filePath);

        if (!$filePath) {
            abort(404, 'File tidak ditemukan.');
        }

        return $this->downloadFile($filePath);
    }

    /**
     * Normalisasi path dan cek file pada storage/app/public.
     */
    private function resolveFilePath(?string $filePath): ?string
    {
        if (!$filePath) {
            return null;
        }

        // File eksternal tidak diproses oleh Storage disk public.
        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            return null;
        }

        $filePath = str_replace('\\', '/', $filePath);
        $filePath = ltrim($filePath, '/');

        // Support path: storage/xxx dan public/xxx.
        foreach (['storage/', 'public/'] as $prefix) {
            if (str_starts_with($filePath, $prefix)) {
                $filePath = substr($filePath, strlen($prefix));
                break;
            }
        }

        if (!Storage::disk('public')->exists($filePath)) {
            return null;
        }

        return $filePath;
    }

    /**
     * Kirim file asli tanpa watermark dan tanpa ZIP.
     */
    private function downloadFile(string $filePath)
    {
        $fullPath = Storage::disk('public')->path($filePath);

        return response()->download(
            $fullPath,
            basename($filePath),
            [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }
}