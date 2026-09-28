@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="flex flex-col md:flex-row items-center justify-center">
    <div class="order-2 md:order-1 md:w-1/2 max-w-[400px] w-full flex flex-col">
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
          <input type="password" id="password" name="password" placeholder="••••••••" required>
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
        <span>Login With SSO</span>
      </div>

      <div class="social-login">
        <a href="{{ route('social.redirect', 'google') }}" class="social-button">
          <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="24px" height="24px">
          <span>Continue With Google</span>
        </a>

        <a href="{{ route('social.redirect', 'facebook') }}" class="social-button">
          <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook" width="24px" height="24px">
          <span>Continue With Facebook</span>
        </a>
      </div>
    </div>

    <div class="flex order-1 md:order-2 md:max-w-100 justify-center w-full">
      <img src="{{ asset('images/login-illustration.svg') }}" alt="Login Illustration"
        class="w-37.5 md:w-full max-w-100">
    </div>
  </div>
@endsection
