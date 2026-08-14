@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="mobile-screen">
    <header class="top-bar">
        <button class="icon-btn"><i class="bi bi-list"></i></button>
        <div class="profile-badge">J</div>
    </header>

    <div class="mobile-card">
        <h1>Notification</h1>
        <p>Hari ini</p>

        <div class="notification-card">
            <div class="notification-item">
                <strong>Rangga P. menyukai postingan anda</strong>
                <small>5 menit lalu</small>
            </div>
            <div class="notification-item">
                <strong>Pengumuman baru di Book Club ID</strong>
                <small>kemarin</small>
            </div>
            <div class="notification-item">
                <strong>Sinta W. telah membalas komentar Anda</strong>
                <small>2 hari lalu</small>
            </div>
        </div>
    </div>
</div>
@endsection