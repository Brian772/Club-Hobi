@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
@php
    $categories = $categories ?? ['Music', 'Hiking', 'Fishing', 'Gaming', 'Football', 'Reading', 'Traveling', 'Swimming', 'Photography'];
@endphp

@if($step == 1)
<div class="auth-screen auth-register auth-register-step1">
    <div class="auth-mobile-shell">
        <header class="auth-header">
            <div class="brand">
                <img src="{{ asset('image/rafiki.png') }}" alt="Orbii logo">
                <span>orbii</span>
            </div>
            <a href="{{ route('login') }}" class="auth-link">Login</a>
        </header>

        <section class="auth-hero auth-hero-register">
            <img src="{{ asset('image/rafiki.png') }}" alt="Orbii onboarding illustration">
        </section>

        <section class="auth-card">
            <div class="card-top">
                <div class="eyebrow">Join the orbii</div>
                <h1>Start finding a hobby club that's right for you.</h1>
            </div>

            <form method="POST" action="{{ route('register.step1') }}" class="form">
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

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="********"
                        required>
                    @error('password_confirmation')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="remember-me">
                    <label>
                        <input type="checkbox" name="remember">
                        <span>I agree to the Terms &amp; Conditions and Privacy Policy</span>
                    </label>
                </div>

                <button type="submit" class="button">
                    Next <span class="button-arrow">→</span>
                </button>
            </form>

            <div class="divider">
                <span>Login With SSO</span>
            </div>

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
        </section>
    </div>
</div>

@elseif($step == 2)
<div class="auth-screen auth-register auth-register-step2">
    <div class="auth-mobile-shell">
        <header class="auth-header">
            <div class="brand">
                <img src="{{ asset('image/rafiki.png') }}" alt="Orbii logo">
                <span>orbii</span>
            </div>
            <a href="{{ route('login') }}" class="auth-link">Login</a>
        </header>

        <section class="auth-card">
            <div class="card-top">
                <div class="eyebrow">Tell us about yourself</div>
                <h1>This information will appear on your profile within the club.</h1>
            </div>

            <form method="POST" action="{{ route('register.step2') }}" class="form profile-form" enctype="multipart/form-data">
                @csrf

                <div class="profile-upload">
                    <input type="file" id="avatar_url" name="avatar_url" accept=".jpg,.jpeg,.png" hidden>
                    <label for="avatar_url" class="upload-label">
                        <span class="upload-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 7.5C4 6.67 4.67 6 5.5 6H8L9.2 4.5H14.8L16 6H18.5C19.33 6 20 6.67 20 7.5V17.5C20 18.33 19.33 19 18.5 19H5.5C4.67 19 4 18.33 4 17.5V7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <circle cx="12" cy="12.5" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                            </svg>
                        </span>
                        <span class="upload-copy">
                            <strong>Upload Photo Profile</strong>
                            <small>Choose File · JPG/PNG, max 2MB</small>
                        </span>
                    </label>
                </div>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="@username" required>
                    @error('name')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" placeholder="Tell us a little about yourself...">{{ old('bio') }}</textarea>
                    @error('bio')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="button">
                    Next <span class="button-arrow">→</span>
                </button>
                <button type="submit" name="skip" value="1" class="button button-secondary">
                    Skip For Now
                </button>
            </form>
        </section>

        <section class="auth-hero auth-hero-register">
            <img src="{{ asset('image/rafiki.png') }}" alt="Profile Illustration">
        </section>
    </div>
</div>

@elseif($step == 3)
<div class="auth-screen auth-register auth-register-step3">
    <div class="auth-mobile-shell">
        <header class="auth-header">
            <div class="brand">
                <img src="{{ asset('image/rafiki.png') }}" alt="Orbii logo">
                <span>orbii</span>
            </div>
            <a href="{{ route('dashboard') }}" class="auth-link">Dashboard</a>
        </header>

        <section class="auth-card">
            <div class="card-top">
                <div class="eyebrow">Choose your hobby</div>
                <h1>We'll recommend clubs based on your interests.</h1>
            </div>

            <form method="POST" action="{{ route('register.step3') }}" class="form hobby-form">
                @csrf

                <div class="hobby-grid">
                    @forelse($categories as $category)
                        <label class="hobby-option">
                            <input type="checkbox" name="hobbies[]" value="{{ $category }}" {{ is_array(old('hobbies')) && in_array($category, old('hobbies')) ? 'checked' : '' }}>
                            <span>{{ $category }}</span>
                        </label>
                    @empty
                        <p>Belum ada kategori hobi yang tersedia.</p>
                    @endforelse
                </div>

                @error('hobbies')
                    <small class="error-text">{{ $message }}</small>
                @enderror

                <button type="submit" class="button hobby-submit">
                    Done &amp; Go to Dashboard <span class="button-arrow">→</span>
                </button>
            </form>
        </section>

        <section class="auth-hero auth-hero-register">
            <img src="{{ asset('image/rafiki.png') }}" alt="Hobby Illustration">
        </section>
    </div>
</div>
@endif

@endsection