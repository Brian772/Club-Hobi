@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/mail.css') }}">
@endsection

@section('content')
    <div class="verify-success-page">

        <div class="verify-success-image">
            <img
                src="{{ asset('images/email.png') }}"
                alt="Email berhasil diverifikasi"
            >
        </div>

        <div class="verify-success-content">

            <h1>
                Akun Berhasil Diverifikasi!
            </h1>

            <p class="verify-greeting">
                Selamat datang di Orbii,
                <strong>{{ Auth::user()->name ?? 'Member' }}</strong>.
            </p>

            <p class="verify-description">
                Akun kamu sudah siap digunakan, Yuk segera menjelahi
                Club dan berinteraksi dengan banyak orang..
            </p>

            <a href="{{ route('dashboard') }}" class="dashboard-button">
                Masuk ke Dashboard
                <span>→</span>
            </a>

        </div>

    </div>
@endsection