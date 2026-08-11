@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
{{-- Memanggil CSS dari resources/css/auth.css --}}
@vite(['resources/css/auth.css'])
@endsection

@section('content')
<div class="halaman1">
<div class="page">
    <div class="container-auth">
        <div class="left">
            <h1 class="title">
                Welcome Back To Orbii
            </h1>

            <form method="POST" action="{{ route('login.authenticate') }}" class="form">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Example@example.com"
                        required
                        autofocus>
                    @error('email')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="********"
                        required>
                    @error('password')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="button">
                    Login
                </button>
            </form>

            <div class="divider">
                <span>Login With SSO</span>
            </div>

            <div class="social-login">
                <a href="{{ route('auth.google') }}" class="social-button">
                    <img
                        src="https://www.svgrepo.com/show/475656/google-color.svg"
                        alt="Google">
                    <span>Continue With Google</span>
                </a>

                <a href="{{ route('auth.facebook') }}" class="social-button">
                    <img
                        src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg"
                        alt="Facebook">
                    <span>Continue With Facebook</span>
                </a>
            </div>

            <p class="register-text">
                Don't have an account yet?
                <a href="{{ route('register') }}">
                    Register now
                </a>
            </p>
        </div>

        <div class="right">
            <img
                src="{{ asset('images/pana.png') }}"
                alt="Login Illustration">
        </div>
    </div>
</div>
</div>
@endsection