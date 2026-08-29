@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="post-page">
    <div class="content-header">
        <div>
            <h1>Kontrol Konten</h1>
            <p>Kelola semua postingan yang kamu buat di berbagai club.</p>
        </div>
        <div class="content-actions">
            <a href="{{ route('posts.trash') }}" class="trash-button">
                <i class="fa-regular fa-trash-can"></i> Sampah
            </a>
            <a href="{{ route('posts.create') }}" class="create-post-button">
                <i class="fa-solid fa-plus"></i> Buat Postingan
            </a>
        </div>
    </div>

    <div class="post-statistics">
        <div class="stat-card">
            <span>Total Postingan</span>
            <strong>{{ $totalPosts }}</strong>
        </div>
        <div class="stat-card">
            <span>Total Suka</span>
            <strong><strong>{{ $totalLikes }}</strong></strong> {{-- Nanti dapat dihubungkan dengan $totalLikes --}}
        </div>
    </div>

    <div class="posts-list">
        @forelse ($posts as $post)
            <div class="post-card">
                <div class="post-header">
                    <span class="post-club-badge">{{ $post->club->name }}</span>
                    <span class="post-date">{{ $post->created_at->diffForHumans() }}</span>
                </div>

                <div class="post-body">
                    <h2>{{ $post->title }}</h2>
                    <p>{{ Str::limit($post->content, 120) }}</p>

                    @if ($post->media_url)
                        @php
                            $extension = strtolower(pathinfo($post->media_url, PATHINFO_EXTENSION));
                            $mediaUrl = asset('storage/' . $post->media_url);
                        @endphp

                        <div class="post-media-preview">
                            @if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']))
                                <img src="{{ $mediaUrl }}" alt="{{ $post->title }}" class="post-image">
                            @elseif (in_array($extension, ['mp4', 'mov']))
                                <video controls class="post-video">
                                    <source src="{{ $mediaUrl }}" type="video/{{ $extension }}">
                                </video>
                            @else
                                <a href="{{ $mediaUrl }}" target="_blank" class="post-file">
                                    <i class="fa-solid fa-paperclip"></i> Lihat File Lampiran
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="post-footer">
                    <div class="post-engagement">
                        <span><i class="fa-regular fa-heart"></i>{{ $post->likes_count ?? 0 }}</span>
                        <span><i class="fa-regular fa-comment"></i> {{ $post->comments_count ?? 0 }}</span>
                    </div>
                    
                    <div class="post-control">
                        <a href="{{ route('posts.edit', $post) }}" title="Edit">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('posts.destroy', $post) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Pindahkan postingan ke sampah?')" title="Hapus">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-post">
                <p>Belum ada postingan yang dibuat.</p>
                <a href="{{ route('posts.create') }}">+ Buat Postingan Pertama</a>
            </div>
        @endforelse
    </div>
</div>
@endsection