@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')

<div class="app-layout">

    <aside class="sidebar">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Orbii Logo">
        </div>

        <nav class="sidebar-menu">
            <ul>
                <li>
                    <a href="{{ route('dashboard') }}"><i class="fa-solid fa-border-all"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="#"><i class="fa-regular fa-bell"></i>
                        <span>Notifikasi</span>
                    </a>
                </li>

                <li>
                    <a href="#"><i class="fa-regular fa-comment-dots"></i>
                        <span>Pesan</span>
                    </a>
                </li>

                <li>
                    <a href="#"><i class="fa-regular fa-folder"></i>
                        <span>Club</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <a href="{{ route('settings.index') }}">
                        <i class="fa-solid fa-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="user-avatar">
                @if ($user->avatar_url)
                    <img src="{{ asset('storage/' . $user->avatar_url) }}" alt="Foto Profil">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>

            <span class="user-name">
                <a href="{{ route('settings.profile') }}">{{ $user->name }}</a>
            </span>
        </div>
    </aside>

    <main class="main-content">

        <h1 class="page-title">Settings</h1>
        <div class="settings-list">
            <div class="settings-group">
            {{-- Profile Card --}}
            <a href="{{ route('settings.profile') }}" class="settings-card">
                <div class="card-left">
                    <i class="fa-regular fa-user"></i>
                    <span>Profile</span>
                </div>
                <i class="fa-solid fa-play arrow-icon"></i>
            </a>

            {{-- Account Card --}}
            <a href="{{ route('settings.account') }}" class="settings-card">
                <div class="card-left">
                    <i class="fa-solid fa-lock"></i>
                    <span>Account</span>
                </div>
                <i class="fa-solid fa-play arrow-icon"></i>
            </a>
</div>

            {{-- Logout Card --}}
            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="settings-group">
                @csrf
                <button type="button" class="settings-card logout-card" id="logout-btn">
                    <div class="card-left">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Logout</span>
                    </div>
                </button>
            </form>
        </div>
    </main>
</div>

{{-- Logout Modal --}}
<div class="logout-modal-overlay" id="logout-modal">

    <div class="logout-modal">
        <h3>Are You sure Want To Logout?</h3>

        <div class="logout-modal-buttons">
            <button type="button" class="logout-yes" id="logout-yes">
                YES
            </button>

            <button type="button" class="logout-cancel" id="logout-cancel">
                Cancel
            </button>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const logoutBtn = document.getElementById('logout-btn');
        const logoutModal = document.getElementById('logout-modal');
        const logoutYes = document.getElementById('logout-yes');
        const logoutCancel = document.getElementById('logout-cancel');
        const logoutForm = document.getElementById('logout-form');

        logoutBtn.addEventListener('click', function () {
            logoutModal.classList.add('show');
        });

        logoutCancel.addEventListener('click', function () {
            logoutModal.classList.remove('show');
        });

        logoutYes.addEventListener('click', function () {
            logoutForm.submit();
        });

        logoutModal.addEventListener('click', function (event) {
            if (event.target === logoutModal) {
                logoutModal.classList.remove('show');
            }
        });

    });
</script>

@endsection