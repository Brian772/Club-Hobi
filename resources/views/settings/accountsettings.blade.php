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
        <div class="page-header">
            <a href="{{ route('settings.index') }}" class="back-link">

                <svg class="back-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>

                <h1 class="page-titleP">Account</h1>
            </a>

        </div>

        <div class="account-page-content">
            <div class="form-group-item">
                <label class="input-label">Email</label>

                <div class="account-field-row">
                    <div class="settings-group account-input-group">
                        <input type="email" class="custom-input" value="{{ $user->email }}" readonly>
                    </div>

                    @if ($user->email_verified_at)
                        <span class="verified-badge">
                            <i class="fa-solid fa-circle-check"></i>
                            Verified
                        </span>
                    @else
                        <span class="unverified-badge">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            Belum diverifikasi
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group-item">
                <label class="input-label">Password</label>

                <div class="settings-group">
                    <input type="password" class="custom-input" value="password" readonly>
                </div>
            </div>

            <div class="account-status-card">
                <div class="account-status-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <div class="account-status-text">
                    <strong>Status Akun</strong>
                    <span>Aktif, tidak ada pembatasan</span>
                </div>
            </div>

            <div class="account-danger-actions">
                <button type="button" class="btn-delete-account" onclick="openDeleteAccountModal()">
                    <i class="fa-solid fa-trash"></i>
                    Hapus Akun
                </button>
            </div>
        </div>
    </main>
</div>

<div class="profile-modal-overlay" id="deleteAccountModal">
    <div class="profile-modal delete-account-modal">
        <div class="modal-header">
            <h3>Hapus Akun</h3>

            <button type="button" class="modal-close" onclick="closeDeleteAccountModal()">
                &times;
            </button>
        </div>

        <div class="modal-body">
            <div class="delete-account-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h4 class="delete-account-title">Yakin ingin menghapus akun?</h4>
            <p class="delete-account-description">
                Semua data akun, profile, dan keanggotaan
                club akan dihapus.
                <br>
                <strong>
                    Tindakan ini tidak dapat dibatalkan.
                </strong>
            </p>

            <form action="{{ route('profile.destroy') }}" method="POST" id="deleteAccountForm">
                @csrf
                @method('DELETE')

                <div class="delete-account-password-group">
                    <label for="deleteAccountPassword" class="input-label">
                        Masukkan password untuk konfirmasi
                    </label>

                    <div class="settings-group">
                        <input
                            type="password"
                            name="password"
                            id="deleteAccountPassword"
                            class="custom-input"
                            placeholder="Password"
                            autocomplete="current-password"
                        >
                    </div>

                    @if ($errors->userDeletion->has('password'))
                        <p class="delete-account-error">
                            {{ $errors->userDeletion->first('password') }}
                        </p>
                    @endif
                </div>

                <div class="delete-account-actions">
                    <button type="button" class="btn-delete-cancel" onclick="closeDeleteAccountModal()">
                        Batal
                    </button>

                    <button type="submit" class="btn-delete-confirm">
                        <i class="fa-solid fa-trash"></i>
                        Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeleteAccountModal() {
        document
            .getElementById('deleteAccountModal')
            .classList.add('show');
    }

    function closeDeleteAccountModal() {
        document
            .getElementById('deleteAccountModal')
            .classList.remove('show');
    }

    document.addEventListener('click', function(event) {
        const deleteAccountModal =
            document.getElementById('deleteAccountModal');

        if (event.target === deleteAccountModal) {
            closeDeleteAccountModal();
        }
    });

    @if ($errors->userDeletion->any())
        document.addEventListener('DOMContentLoaded', function() {
            openDeleteAccountModal();
        });
    @endif
</script>

@endsection