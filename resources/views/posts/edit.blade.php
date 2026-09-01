@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
    <div class="post-page">
        <div class="post-page-header">
            <h1 class="header-title">
                <a href="{{ route('posts.index') }}">
                    <svg class="back-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </a>
                Edit Postingan
            </h1>
            <p>Perbarui detail postingan kamu.</p>
        </div>

        <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="club_id">Pilih Club</label>
                <select name="club_id" id="club_id" required>
                    <option value="">-- Pilih Club --</option>
                    @foreach ($clubs as $club)
                        <option value="{{ $club->id }}" @selected(old('club_id', $post->club_id) == $club->id)>
                            {{ $club->name }}
                        </option>
                    @endforeach
                </select>
                @error('club_id')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="title">Judul Postingan</label>
                <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required>
                @error('title')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="content">Isi Postingan</label>
                <textarea name="content" id="content" required>{{ old('content', $post->content) }}</textarea>
                @error('content')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Ganti Media (Opsional)</label>
                <div class="file-upload-wrapper relative overflow-hidden">
                    {{-- Tampilkan Background Samar jika Post Memiliki Media --}}
                    @if ($post->media_url)
                        @php
                            $extension = strtolower(pathinfo($post->media_url, PATHINFO_EXTENSION));
                            $mediaUrl = asset('storage/' . $post->media_url);
                        @endphp

                        <div
                            class="absolute inset-0 z-0 opacity-20 pointer-events-none flex items-center justify-center overflow-hidden">
                            @if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']))
                                <img src="{{ $mediaUrl }}" class="w-full h-full object-cover filter blur-[1px]">
                            @elseif (in_array($extension, ['mp4', 'mov']))
                                <video src="{{ $mediaUrl }}" class="w-full h-full object-cover filter blur-[1px]" muted autoplay
                                    loop></video>
                            @else
                                <div class="flex items-center gap-2 text-neutral-600 font-semibold">
                                    <i class="fa-solid fa-file text-2xl"></i>
                                    <span class="text-xs">{{ basename($post->media_url) }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Input File dan Teks Penjelas (Berada di Atas Background) --}}
                    <input type="file" name="media" id="media" accept="image/*,video/*,audio/*,.pdf,.doc,.docx"
                        class="z-20 relative">
                    <div class="file-upload-design z-10 relative bg-white/60 p-2 rounded-md backdrop-blur-[2px]">
                        <i class="fa-solid fa-cloud-arrow-up text-blue-600"></i>
                        <span class="font-medium text-neutral-800">
                            {{ $post->media_url ? 'Klik / seret file baru untuk mengganti lampiran' : 'Klik untuk mengunggah media' }}
                        </span>
                        @if($post->media_url)
                        @endif
                    </div>
                </div>
                @error('media')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('posts.index') }}" class="cancel-button">Batal</a>
                <button type="submit" class="publish-button">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection