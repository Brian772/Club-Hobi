@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-shell auth-shell-login">
    <div class="auth-box">
        <header class="auth-topbar">
            <div class="orbii-brand">
                <span class="brand-mark">◉</span>
                <span>orbii</span>
            </div>
            <a href="{{ route('register') }}" class="top-link">Need an account? <strong>Register</strong></a>
        </header>

        <div class="auth-panel">
            <section class="auth-form-area">
                <div class="eyebrow">Login</div>
                <h1 class="auth-title">Welcome back to orbii</h1>
                <p class="auth-subtext">Masuk untuk menemukan komunitas hobi yang sesuai dengan minatmu.</p>

                @if(session('status'))
                    <div class="alert alert-info">{{ session('status') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('login.authenticate') }}" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Example@example.com" required autofocus>
                        @error('email')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="********" required>
                        @error('password')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="button-primary">Login</button>
                </form>

                <div class="divider"><span>Login With SSO</span></div>

                <div class="social-login">
                    <a href="{{ route('auth.google') }}" class="social-button">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                        <span>Continue With Google</span>
                    </a>

                    <a href="{{ route('auth.facebook') }}" class="social-button">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook">
                        <span>Continue With Facebook</span>
                    </a>
                </div>

                <p class="register-text">Don't have an account yet? <a href="{{ route('register') }}">Register now</a></p>
            </section>

            <aside class="auth-media">
                <img src="{{ asset('image/pana.png') }}" alt="Login Illustration">
            </aside>
        </div>
    </div>
</div>
@endsection