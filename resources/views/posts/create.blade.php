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
                Buat Postingan
            </h1>
            <p>Bagikan aktivitas atau konten ke club yang kamu ikuti.</p>
        </div>

            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="club_id">Pilih Club</label>
                    <select name="club_id" id="club_id" required>
                        <option value="">-- Pilih Club --</option>
                        @foreach ($clubs as $club)
                            <option value="{{ $club->id }}" @selected(old('club_id') == $club->id)>
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
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Masukkan judul postingan" required>
                    @error('title')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="content">Isi Postingan</label>
                    <textarea name="content" id="content" placeholder="Apa yang ingin kamu bagikan?" required>{{ old('content') }}</textarea>
                    @error('content')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Upload Media (Opsional)</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="media" id="media" accept="image/*,video/*,audio/*,.pdf,.doc,.docx">
                        <div class="file-upload-design">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Pilih foto, video, atau dokumen</span>
                            <small>Maksimal 20MB</small>
                        </div>
                    </div>
                    @error('media')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('posts.index') }}" class="cancel-button">Batal</a>
                    <button type="submit" class="publish-button">
                        <i class="fa-solid fa-paper-plane"></i> Publikasikan Postingan
                    </button>
                </div>
            </form>
</div>
@endsection