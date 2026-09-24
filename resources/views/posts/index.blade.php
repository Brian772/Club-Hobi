@extends('layouts.app')

@section('title', 'Orbii | Posts')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="post-page max-w-7xl mx-auto px-4 py-6">
    <div class="content-header flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Riwayat Konten</h1>
            <p class="text-xs text-neutral-500">Kelola semua postingan yang kamu buat di berbagai club.</p>
        </div>
        <div class="content-actions flex items-center gap-3">
            <a href="{{ route('posts.trash') }}" class="trash-button border border-neutral-300 text-neutral-700 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-neutral-100 flex items-center gap-1.5">
                <i class="fa-regular fa-trash-can"></i> Sampah
            </a>
            <a href="{{ route('posts.create') }}" class="create-post-button bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-700 flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Buat Postingan
            </a>
        </div>
    </div>

    <div class="post-statistics grid grid-cols-2 gap-4 mb-6">
        <div class="stat-card bg-white p-4 rounded-xl border border-neutral-200">
            <span class="text-xs text-neutral-500 block mb-1">Total Postingan</span>
            <strong class="text-xl font-bold text-neutral-900">{{ $totalPosts }}</strong>
        </div>
        <div class="stat-card bg-white p-4 rounded-xl border border-neutral-200">
            <span class="text-xs text-neutral-500 block mb-1">Total Suka</span>
            <strong class="text-xl font-bold text-neutral-900">{{ $totalLikes ?? 0 }}</strong>
        </div>
    </div>

    {{-- Grid 3 Kolom per Baris --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($posts as $post)
            <div class="post-card bg-white rounded-xl border border-neutral-200 overflow-hidden flex flex-col justify-between shadow-sm">
                <div>
                    <div class="post-header p-3 border-b border-neutral-100 flex items-center justify-between">
                        <span class="post-club-badge bg-blue-50 text-blue-600 text-[10px] font-semibold px-2 py-0.5 rounded-full">{{ $post->club->name }}</span>
                        <span class="post-date text-[10px] text-neutral-400">{{ $post->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="post-body p-3.5 space-y-2">
                        <h2 class="text-sm font-bold text-neutral-900 line-clamp-1">{{ $post->title }}</h2>
                        <p class="text-xs text-neutral-600 leading-relaxed line-clamp-2">{{ $post->content }}</p>

                        @php
                            $mediaItems = collect();
                            if (isset($post->media) && count($post->media) > 0) {
                                $mediaItems = $post->media;
                            } elseif (!empty($post->media_url)) {
                                $mediaItems = collect([(object)['file_path' => $post->media_url]]);
                            }
                        @endphp

                        @if ($mediaItems->isNotEmpty())
                            @php
                                $firstMedia = $mediaItems->first();
                                $filePath = $firstMedia->file_path ?? $firstMedia;
                                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $mediaUrl = asset('storage/' . $filePath);
                            @endphp

                            <div class="post-media-preview h-40 w-full rounded-lg overflow-hidden bg-neutral-100 relative mt-2">
                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']))
                                    <img src="{{ $mediaUrl }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                                @elseif (in_array($extension, ['mp4', 'mov', 'webm']))
                                    <video class="w-full h-full object-cover" muted>
                                        <source src="{{ $mediaUrl }}" type="video/{{ $extension }}">
                                    </video>
                                @else
                                    <a href="{{ $mediaUrl }}" target="_blank" class="w-full h-full flex flex-col items-center justify-center gap-1 text-xs text-neutral-600 hover:text-blue-600">
                                        <i class="fa-solid fa-paperclip text-xl"></i> Lampiran File
                                    </a>
                                @endif

                                @if ($mediaItems->count() > 1)
                                    <span class="absolute top-2 right-2 bg-black/70 text-white text-[10px] px-2 py-0.5 rounded-full font-medium">
                                        +{{ $mediaItems->count() - 1 }} media
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="post-footer p-3 bg-neutral-50 border-t border-neutral-100 flex items-center justify-between text-xs">
                    <div class="post-engagement flex items-center gap-3 text-neutral-500">
                        <span><i class="fa-regular fa-heart mr-1"></i>{{ $post->likes_count ?? 0 }}</span>
                        <span><i class="fa-regular fa-comment mr-1"></i>{{ $post->comments_count ?? 0 }}</span>
                    </div>
                    
                    <div class="post-control flex items-center gap-2">
                        <a href="{{ route('posts.edit', $post) }}" title="Edit" class="text-neutral-500 hover:text-blue-600 p-1">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Pindahkan postingan ke sampah?')" title="Hapus" class="text-neutral-500 hover:text-red-600 p-1">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-post col-span-full text-center py-12 bg-white rounded-xl border border-neutral-200">
                <p class="text-xs text-neutral-500 mb-3">Belum ada postingan yang dibuat.</p>
                <a href="{{ route('posts.create') }}" class="text-xs text-blue-600 font-semibold hover:underline">+ Buat Postingan Pertama</a>
            </div>
        @endforelse
    </div>
</div>
@endsection