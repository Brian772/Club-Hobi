@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')

<div class="app-layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Orbii Logo">
        </div>

        <nav class="sidebar-menu">
            <ul>

                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="fa-solid fa-border-all"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa-regular fa-bell"></i>
                        <span>Notifikasi</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa-regular fa-comment-dots"></i>
                        <span>Pesan</span>
                    </a>
                </li>

                <li class="active">
                    <a href="#">
                        <i class="fa-regular fa-folder"></i>
                        <span>Konten</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('settings.index') }}">
                        <i class="fa-solid fa-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>

            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="user-avatar">J</div>

            <span class="user-name">
                <a href="#">Jhon</a>
            </span>
        </div>

    </aside>


    {{-- MAIN CONTENT --}}
    <main class="main-content">

        <div class="page-header">

            <a href="{{ route('dashboard') }}" class="back-link">

                <svg class="back-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>

                </svg>

                <span>Kembali</span>

            </a>

            <h1 class="page-title">
                Tambah Konten
            </h1>

            <p class="page-description">
                Tambahkan dokumentasi baru ke dalam club.
            </p>

        </div>


        {{-- FORM --}}
        <div class="content-form-card">

            <div class="form-header">
                <div class="form-icon">
                    <i class="fa-regular fa-folder-open"></i>
                </div>

                <div>
                    <h2>Dokumentasi Baru</h2>
                    <p>
                        Masukkan informasi dan file dokumentasi yang ingin ditambahkan.
                    </p>
                </div>
            </div>


            <form action="{{ route('konten.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf


                {{-- CLUB --}}
                <div class="form-group">

                    <label for="club_id">
                        Club
                    </label>

                    <select name="club_id"
                            id="club_id"
                            required>

                        <option value="" selected disabled>
                            Pilih club
                        </option>

                        {{-- Contoh --}}
                        <option value="1">
                            Anime Club
                        </option>

                        <option value="2">
                            Gaming Club
                        </option>

                        <option value="3">
                            Music Club
                        </option>

                    </select>

                </div>


                {{-- TITLE --}}
                <div class="form-group">

                    <label for="title">
                        Judul Konten
                    </label>

                    <input
                        type="text"
                        name="title"
                        id="title"
                        placeholder="Contoh: Dokumentasi kegiatan club"
                        value="{{ old('title') }}"
                        required
                    >

                    @error('title')
                        <span class="form-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                {{-- TYPE --}}
                <div class="form-group">

                    <label for="type">
                        Jenis Konten
                    </label>

                    <select name="type"
                            id="type"
                            required>

                        <option value="" selected disabled>
                            Pilih jenis konten
                        </option>

                        <option value="image">
                            Gambar
                        </option>

                        <option value="video">
                            Video
                        </option>

                        <option value="document">
                            Dokumen
                        </option>

                    </select>

                    @error('type')
                        <span class="form-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                {{-- FILE UPLOAD --}}
                <div class="form-group">

                    <label for="file">
                        File Dokumentasi
                    </label>

                    <div class="upload-box">

                        <input
                            type="file"
                            name="file"
                            id="file"
                            required
                        >

                        <div class="upload-content">

                            <div class="upload-icon">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>

                            <strong>
                                Pilih file untuk diupload
                            </strong>

                            <span>
                                Gambar, video, atau dokumen
                            </span>

                        </div>

                    </div>

                    @error('file')
                        <span class="form-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                {{-- BUTTON --}}
                <div class="form-actions">

                    <a href="{{ route('dashboard') }}"
                       class="cancel-button">
                        Batal
                    </a>

                    <button type="submit"
                            class="save-button">

                        <i class="fa-solid fa-upload"></i>

                        Upload Konten

                    </button>

                </div>

            </form>

        </div>

    </main>

</div>

@endsection