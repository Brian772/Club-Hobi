@extends('layouts.app')

@section('title', 'Orbii | Login')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endsection

@section('content')
  <div class="flex flex-col md:flex-row items-center justify-center">
    <div class="order-2 md:order-1 md:w-120 w-full flex flex-col">
      <h1 class="text-[26px] font-bold text-primary">
        Welcome Back To Orbii
      </h1>

      @if (session('warning'))
        <div class="p-3 mb-4 text-sm text-amber-800 bg-amber-100 rounded-lg" role="alert">
          {{ session('warning') }}
        </div>
      @endif

      @if (session('success'))
        <div class="p-3 mb-4 text-sm text-green-800 bg-green-100 rounded-lg" role="alert">
          {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login.authenticate') }}" data-turbo="false" class="form">
        @csrf
        <div class="form-group">
          <x-input-label for="email" :value="__('Email')" />
          <input type="email" id="email" name="email" value="{{ old('email') }}"
            placeholder="example@example.com" required autofocus>
          @error('email')
            <small class="error-text">{{ $message }}</small>
          @enderror
        </div>

        <div class="form-group">
          <x-input-label for="password" :value="__('Password')" />
          <div class="relative flex items-center">
            <input type="password" id="password" name="password" placeholder="••••••••" class="w-full pr-10" required>
            <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 text-neutral-500 hover:text-neutral-700 focus:outline-none">
              <i class="bi bi-eye-slash text-lg"></i>
            </button>
          </div>
          @error('password')
            <small class="error-text">{{ $message }}</small>
          @enderror
        </div>

        <x-secondary-button type="submit" class="mt-6">
          Login
        </x-secondary-button>
        <p class="register-text">
          Don't have an account yet?
          <a href="{{ route('register') }}">
            Register now
          </a>
        </p>
      </form>

      <div class="divider">
        <span>Or Login With</span>
      </div>

      <div class="social-login">
        <a href="{{ route('social.redirect', 'google') }}" class="social-button">
          <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="24px" height="24px">
          <span>Continue With Google</span>
        </a>
      </div>
    </div>

    <div class="flex order-1 md:order-2 md:max-w-100 justify-center w-full">
      <img src="{{ asset('images/Login-illustration.svg') }}" alt="Login Illustration"
        class="w-37.5 md:w-full max-w-100">
    </div>
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