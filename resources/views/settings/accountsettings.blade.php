@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
    {{-- Header Halaman --}}
    <div class="page-header">
        <a href="{{ route('settings.index') }}" class="back-link">
            <svg class="back-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <h1 class="page-titleP">Account</h1>
        </a>
    </div>

    {{-- Group Email --}}
    <div class="form-group-item">
        <label class="input-label">Email</label>
        <div class="input-verified-wrapper">
            <input type="email" class="custom-input-box" value="{{ $user->email }}" readonly>
            <div class="account-status-card">
                <div class="account-status-icon">
                    {{-- Pengecekan Real dari Database --}}
                    @if ($user->hasVerifiedEmail())
                        <span class="verified-badge">
                            <i class="fa-solid fa-circle-check"></i>
                            Verified
                        </span>
                    @else
                        <span class="unverified-badge">
                            <i class="fa-solid fa-circle-xmark"></i>
                            Belum Diverifikasi
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Group Password --}}
    <div class="form-group-item">
        <label class="input-label">Password</label>
        <input type="password" class="custom-input-box" value="********" readonly>
    </div>

    {{-- Status Akun Card --}}
    <div class="account-status-card">
        <div class="account-status-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="account-status-text">
            <strong>Status Akun</strong>
            <span>Aktif, tidak ada pembatasan</span>
        </div>
    </div>

    {{-- Tombol Hapus --}}
    <div class="account-danger-actions">
        <button type="button" class="btn-delete-account" onclick="openDeleteAccountModal()">
            <i class="fa-solid fa-trash"></i>
            Hapus Akun
        </button>
    </div>
    </div>

    {{-- Modal Hapus Akun --}}
    <div class="profile-modal-overlay" id="deleteAccountModal">
        <div class="profile-modal delete-account-modal">
            <div class="modal-header">
                <h3>Hapus Akun</h3>
                <button type="button" class="modal-close" onclick="closeDeleteAccountModal()">&times;</button>
            </div>

            <div class="modal-body">
                <div class="delete-account-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h4 class="delete-account-title">Yakin ingin menghapus akun?</h4>
                <p class="delete-account-description">
                    Semua data akun, profile, dan keanggotaan club akan dihapus.<br>
                    <strong>Tindakan ini tidak dapat dibatalkan.</strong>
                </p>

                <form action="{{ route('profile.destroy') }}" method="POST" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')

<<<<<<< HEAD
                    <div class="mb-4">
                        <input
                            type="password"
                            name="password"
                            id="deleteAccountPassword"
                            class="rounded-full border border-hairline focus-within:border-blue-500 focus-within:ring-3 focus-within:ring-blue-200"
                            placeholder="Password"
                            autocomplete="current-password"
                        >
=======
                    <div class="delete-account-password-group" style="margin-bottom: 20px; text-align: left;">
                        <label for="deleteAccountPassword" class="input-label">
                            Masukkan password untuk konfirmasi
                        </label>
                        <input type="password" name="password" id="deleteAccountPassword" class="custom-input-box"
                            placeholder="Password" autocomplete="current-password" style="margin-top: 6px;">
                        @if ($errors->userDeletion->has('password'))
                            <p class="delete-account-error" style="color: #dc2626; font-size: 13px; margin-top: 6px;">
                                {{ $errors->userDeletion->first('password') }}
                            </p>
                        @endif
>>>>>>> e9177dea4c1cb9a48a7b9284d3bf53cb2524b5b4
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


        <script>
            function openDeleteAccountModal() {
                document.getElementById('deleteAccountModal').classList.add('show');
            }

            function closeDeleteAccountModal() {
                document.getElementById('deleteAccountModal').classList.remove('show');
            }

<<<<<<< HEAD
        if (event.target === deleteAccountModal) {
            closeDeleteAccountModal();
        }
    });

    @if ($errors->userDeletion->any())
        document.addEventListener('turbo:load', function() {
            openDeleteAccountModal();
        });
    @endif
</script>
=======
            document.addEventListener('click', function (event) {
                const deleteAccountModal = document.getElementById('deleteAccountModal');
                if (event.target === deleteAccountModal) {
                    closeDeleteAccountModal();
                }
            });
>>>>>>> e9177dea4c1cb9a48a7b9284d3bf53cb2524b5b4

            @if ($errors->userDeletion->any())
                document.addEventListener('DOMContentLoaded', function () {
                    openDeleteAccountModal();
                });
            @endif
        </script>
@endsection