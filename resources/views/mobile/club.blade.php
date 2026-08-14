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
        <h1>Club</h1>
        <p>Lihat lebih lengkap klub hobi yang kamu minati</p>
    </div>

    <div class="club-list">
        <div class="club-card">
            <strong>Street Photography</strong>
            <small>Photography · 459 anggota</small>
            <a href="#" class="join-btn">Lihat selengkapnya →</a>
        </div>
        <div class="club-card">
            <strong>Fishing Indonesia</strong>
            <small>Fishing · 926 anggota</small>
            <a href="#" class="join-btn">Lihat selengkapnya →</a>
        </div>
        <div class="club-card">
            <strong>Book Club ID</strong>
            <small>Reading · 531 anggota</small>
            <a href="#" class="join-btn">Lihat selengkapnya →</a>
        </div>
    </div>
</div>
@endsection