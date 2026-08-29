<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $posts = Post::where('user_id', $user->id)
            ->with('club')
            ->withCount(['comments', 'likes'])
            ->latest()
            ->get();

        $totalPosts = $posts->count(); //[cite: 2]

        // Tambahkan perhitungan total suka dari semua postingan milik user:
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
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mov,mp3,wav,pdf,doc,docx', 'max:20480'],
        ]);

        $user = Auth::user();
        $club = $user->clubs()->where('clubs.id', $validated['club_id'])->firstOrFail();

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('posts', 'public');
        }

        Post::create([
            'club_id' => $club->id,
            'user_id' => $user->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'media_url' => $mediaPath,
        ]);

        return redirect()->route('posts.index')->with('success', 'Postingan berhasil dibuat.');
    }

    public function edit(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

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
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mov,mp3,wav,pdf,doc,docx', 'max:20480'],
        ]);

        $user = Auth::user();
        $club = $user->clubs()->where('clubs.id', $validated['club_id'])->firstOrFail();

        $data = [
            'club_id' => $club->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
        ];

        if ($request->hasFile('media')) {
            if ($post->media_url) {
                Storage::disk('public')->delete($post->media_url);
            }
            $data['media_url'] = $request->file('media')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()->route('posts.index')->with('success', 'Postingan berhasil diperbarui.');
    }

    public function like(Post $post)
    {
        $userId = Auth::id();

        $existingLike = Like::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            Like::create([
                'post_id' => $post->id,
                'user_id' => $userId,
            ]);
        }

        return back();
    }

    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        Comment::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id ?? null,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    public function destroyComment(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();
        return back()->with('success', 'Komentar berhasil dihapus!');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Postingan dipindahkan ke sampah.');
    }

    public function trash()
    {
        $posts = Post::onlyTrashed()
            ->where('user_id', Auth::id())
            ->with('club')
            ->latest('deleted_at')
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

        return redirect()->route('posts.trash')->with('success', 'Postingan berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($post->media_url) {
            Storage::disk('public')->delete($post->media_url);
        }

        $post->forceDelete();

        return redirect()->route('posts.trash')->with('success', 'Postingan dihapus secara permanen.');
    }
}