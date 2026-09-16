<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostMedia;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;

class PostController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $posts = Post::where('user_id', $user->id)
            ->with(['club', 'media'])
            ->withCount(['comments', 'likes'])
            ->latest()
            ->get();

        $totalPosts = $posts->count();
        $totalLikes = $posts->sum('likes_count');

        return view('posts.index', compact('posts', 'totalPosts', 'totalLikes'));
    }

    public function create()
    {
        $clubs = Auth::user()->clubs;
        return view('posts.create', compact('clubs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'club_id' => ['required'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'media' => ['nullable', 'array'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,mp3,wav,pdf,doc,docx', 'max:20480'],
        ]);

        $user = Auth::user();
        $club = $user->clubs()->where('clubs.id', $validated['club_id'])->firstOrFail();

        $post = Post::create([
            'club_id' => $club->id,
            'user_id' => $user->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $mediaPath = null;

                if (in_array($extension, ['mp4', 'mov', 'avi'])) {
                    $fileName = time() . '_' . uniqid() . '_converted.mp4';
                    $outputPath = storage_path('app/public/posts/' . $fileName);

                    $ffmpeg = FFMpeg::create([
                        'ffmpeg.binaries' => 'D:/laragon/bin/ffmpeg/ffmpeg.exe',
                        'ffprobe.binaries' => 'D:/laragon/bin/ffmpeg/ffprobe.exe',
                        'timeout' => 3600,
                        'ffmpeg.threads' => 12,
                    ]);

                    $video = $ffmpeg->open($file->getRealPath());
                    $format = new X264();
                    $format->setAudioCodec('aac');
                    $video->save($format, $outputPath);

                    $mediaPath = 'posts/' . $fileName;
                } else {
                    $mediaPath = $file->store('posts', 'public');
                }

                PostMedia::create([
                    'post_id' => $post->id,
                    'file_path' => $mediaPath,
                    'file_type' => $extension,
                ]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Postingan berhasil dibuat.');
    }

    public function edit(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->load('media');
        $clubs = Auth::user()->clubs;
        return view('posts.edit', compact('post', 'clubs'));
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'club_id' => ['required'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'media' => ['nullable', 'array'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,mp3,wav,pdf,doc,docx', 'max:20480'],
            'delete_media' => ['nullable', 'array'],
            'delete_media.*' => ['exists:post_media,id'],
        ]);

        $user = Auth::user();
        $club = $user->clubs()->where('clubs.id', $validated['club_id'])->firstOrFail();

        $post->update([
            'club_id' => $club->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        if ($request->has('delete_media')) {
            $mediaToDelete = PostMedia::whereIn('id', $request->delete_media)->where('post_id', $post->id)->get();
            foreach ($mediaToDelete as $media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
            }
        }

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $mediaPath = $file->store('posts', 'public');

                PostMedia::create([
                    'post_id' => $post->id,
                    'file_path' => $mediaPath,
                    'file_type' => $extension,
                ]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Postingan berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Postingan dipindahkan ke sampah.');
    }

    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        foreach ($post->media as $media) {
            Storage::disk('public')->delete($media->file_path);
        }

        $post->forceDelete();

        return redirect()->route('posts.trash')->with('success', 'Postingan dihapus secara permanen.');
    }

    public function trash()
    {
        $posts = Post::onlyTrashed()
            ->where('user_id', Auth::id())
            ->with(['club', 'media'])
            ->latest()
            ->get();

        return view('posts.trash', compact('posts'));
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $post->restore();

        return redirect()->route('posts.index')->with('success', 'Postingan berhasil dikembalikan.');
    }

    public function like(Request $request, Post $post)
    {
        $userId = Auth::id();

        $existingLike = Like::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            Like::create([
                'post_id' => $post->id,
                'user_id' => $userId,
            ]);
            $liked = true;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => $post->likes()->count(),
            ]);
        }

        return back();
    }

    public function storeComment(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        $comment->load('user');

        return response()->json([
            'id' => $comment->id,
            'post_id' => $comment->post_id,
            'user_id' => $comment->user_id,
            'parent_id' => $comment->parent_id,
            'content' => $comment->content,
            'created_at' => $comment->created_at,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'avatar_full_url' => $comment->user->avatar_full_url,
            ],
        ], 201);
    }

    public function destroyComment(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();
        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}