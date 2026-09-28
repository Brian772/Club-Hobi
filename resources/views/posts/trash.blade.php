@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="post-page">
    <div class="content-header">
        <div>
            <h1 class="header-title">
                <a href="{{ route('posts.index') }}" class="back-arrow">
                    <svg class="back-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </a>
                Sampah Postingan
            </h1>
            <p>Postingan akan dihapus permanen setelah 30 hari.</p>
        </div>
    </div>

    <div class="trash-list">
        @forelse ($posts as $post)
            <div class="trash-post-card">
                <div class="trash-post-info">
                    <h2>{{ $post->title }}</h2>
                    <p>Dihapus {{ $post->deleted_at->diffForHumans() }}</p>
                </div>
                <div class="trash-actions">
                    <form action="{{ route('posts.restore', $post->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-restore">
                            <i class="fa-solid fa-rotate-left"></i> Pulihkan
                        </button>
                    </form>

                    <form action="{{ route('posts.force-delete', $post->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-force-delete" onclick="return confirm('Hapus permanen?')">
                            <i class="fa-regular fa-trash-can"></i> Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-post">
                <p>Sampah masih kosong.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection