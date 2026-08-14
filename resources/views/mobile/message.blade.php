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
        <h1>Pesan</h1>
        <p>Pesan terbaru dari teman dan klub.</p>
    </div>

    <div class="message-row">
        <div class="message-item">
            <div class="message-top">
                <div class="message-avatar">S</div>
                <small>2 hari lalu</small>
            </div>
            <div class="message-content">
                <strong>Sinta W.</strong>
                <p>wkwkwk</p>
            </div>
        </div>
        <div class="message-item">
            <div class="message-top">
                <div class="message-avatar">R</div>
                <small>2 hari lalu</small>
            </div>
            <div class="message-content">
                <strong>Rangga P.</strong>
                <p>Mau ikut photoshoot?</p>
            </div>
        </div>
    </div>
</div>
@endsection