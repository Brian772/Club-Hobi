@extends('layouts.app')

@section('title', 'Orbii | Ubah Password')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
    <div class="pt-6">
        <div class="page-header">
            <a href="{{ route('settings.account') }}" class="back-link">
                <svg class="back-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <h1 class="page-titleP">Ubah Password</h1>
            </a>
        </div>

        <form method="POST" action="{{ route('settings.password.update') }}" class="mt-8 max-w-xl">
            @csrf
            @method('PUT')

            <div class="form-group-item">
                <label for="current_password" class="input-label">Password Saat Ini</label>
                <div class="relative flex items-center mt-1">
                    <input type="password" id="current_password" name="current_password" class="custom-input-box w-full pr-10" required autofocus autocomplete="current-password">
                    <button type="button" onclick="togglePassword('current_password', this)" class="absolute right-3 text-neutral-500 hover:text-neutral-700 focus:outline-none cursor-pointer">
                        <i class="bi bi-eye-slash text-lg"></i>
                    </button>
                </div>
                @error('current_password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group-item">
                <label for="password" class="input-label">Password Baru</label>
                <div class="relative flex items-center mt-1">
                    <input type="password" id="password" name="password" class="custom-input-box w-full pr-10" required autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 text-neutral-500 hover:text-neutral-700 focus:outline-none cursor-pointer">
                        <i class="bi bi-eye-slash text-lg"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group-item">
                <label for="password_confirmation" class="input-label">Konfirmasi Password Baru</label>
                <div class="relative flex items-center mt-1">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="custom-input-box w-full pr-10" required autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-3 text-neutral-500 hover:text-neutral-700 focus:outline-none cursor-pointer">
                        <i class="bi bi-eye-slash text-lg"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-4 mt-6">
                <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary/90 transition-colors cursor-pointer">
                    Simpan Password
                </button>
                <a href="{{ route('settings.account') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(fieldId, button) {
            const inputField = document.getElementById(fieldId);
            const icon = button.querySelector('i');

            if (inputField.type === "password") {
                inputField.type = "text";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            } else {
                inputField.type = "password";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            }
        }
    </script>
@endsection