@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
    <div class="flex flex-col w-full">
        <div class="page-header">
            <a href="{{ route('settings.index') }}" class="back-link">

                <svg class="back-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>

                <h1 class="text-ink text-heading-2 font-bold">Account</h1>
            </a>

        </div>

        <div class="account-page-content">
            <div class="form-group-item">
                <label class="input-label">Email</label>

                <div class="flex flex-col lg:flex-row gap-2 ">
                    <div class="account-input-group">
                        <input type="email" class="rounded-full w-full border border-hairline focus-within:border-blue-500 focus-within:ring-3 focus-within:ring-blue-200 select-none cursor-default" value="{{ $user->email }}" readonly>
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

                <div class="settings-group cursor-default">
                    <input type="password" class="rounded-full border w-full border-hairline focus-within:border-blue-500 focus-within:ring-3 focus-within:ring-blue-200 select-none cursor-default" value="password" readonly>
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

                    <div class="mb-4">
                        <input
                            type="password"
                            name="password"
                            id="deleteAccountPassword"
                            class="rounded-full border border-hairline focus-within:border-blue-500 focus-within:ring-3 focus-within:ring-blue-200"
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
        document.addEventListener('turbo:load', function() {
            openDeleteAccountModal();
        });
    @endif
</script>

@endsection