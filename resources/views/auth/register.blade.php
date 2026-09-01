@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-shell auth-shell-register">
    <div class="auth-box">
        <header class="auth-topbar">
            <div class="orbii-brand">
                <span class="brand-mark">◉</span>
                <span>orbii</span>
            </div>
            <a href="{{ route('login') }}" class="top-link">Have an account? <strong>Login</strong></a>
        </header>

        @if($step == 1)
            <div class="auth-panel">
                <section class="auth-form-area">
                    <h1 class="auth-title">Join the orbii</h1>
                    <p class="auth-subtext">Start finding a hobby club that's right for you.</p>

                    <div class="progress-steps" aria-label="Registration progress">
                        <span class="progress-step active"></span>
                        <span class="progress-step"></span>
                        <span class="progress-step"></span>
                    </div>

                    <form method="POST" action="{{ route('register.step1') }}" class="auth-form">
                        @csrf

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Example@example.com" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="********" required>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********" required>
                        </div>

                        <label class="checkbox-row">
                            <input type="checkbox" name="agree" value="1">
                            <span>I agree to the Terms &amp; Conditions and Privacy Policy</span>
                        </label>

                        <button type="submit" class="button-primary">Next <span>→</span></button>
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
                </section>

                <aside class="auth-media">
                    <img src="{{ asset('image/amico1.png') }}" alt="Registration Illustration">
                </aside>
            </div>

        @elseif($step == 2)
            <div class="auth-panel">
                <section class="auth-form-area">
                    <h1 class="auth-title">Tell us about yourself</h1>
                    <p class="auth-subtext">This information will appear on your profile within the club.</p>

                    <div class="progress-steps" aria-label="Registration progress">
                        <span class="progress-step"></span>
                        <span class="progress-step active"></span>
                        <span class="progress-step"></span>
                    </div>

                    <form method="POST" action="{{ route('register.step2') }}" class="auth-form" enctype="multipart/form-data">
                        @csrf

                        <label for="avatar_url" class="upload-box">
                            <span class="upload-icon">📷</span>
                            <span class="upload-copy">
                                <strong>Upload Photo Profile</strong>
                                <small>Choose File · JPG/PNG, max 2MB</small>
                            </span>
                        </label>
                        <input type="file" id="avatar_url" name="avatar_url" accept=".jpg,.jpeg,.png" hidden>

                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="@username" required>
                        </div>

                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" name="bio" rows="4" placeholder="Tell us a little about yourself...">{{ old('bio') }}</textarea>
                        </div>

                        <button type="submit" class="button-primary">Next <span>→</span></button>
                        <a href="{{ route('register', ['step' => 3]) }}" class="skip-link">Skip For Now</a>
                    </form>
                </section>

                <aside class="auth-media">
                    <img src="{{ asset('image/amico2.png') }}" alt="Profile Illustration" onerror="this.onerror=null;this.src='{{ asset('image/pana.png') }}';">
                </aside>
            </div>

        @elseif($step == 3)
            <div class="auth-panel">
                <section class="auth-form-area">
                    <h1 class="auth-title">Choose your hobby</h1>
                    <p class="auth-subtext">We’ll recommend clubs based on your interests.</p>

                    <div class="progress-steps" aria-label="Registration progress">
                        <span class="progress-step"></span>
                        <span class="progress-step"></span>
                        <span class="progress-step active"></span>
                    </div>

                    <form method="POST" action="{{ route('register.step3') }}" class="auth-form hobby-form">
                        @csrf

                        <div class="hobby-grid">
                            @forelse($categories as $category)
                                <label class="hobby-option">
                                    <input type="checkbox" name="hobbies[]" value="{{ $category }}">
                                    <span>{{ $category }}</span>
                                </label>
                            @empty
                                <p>Belum ada kategori hobi yang tersedia.</p>
                            @endforelse
                        </div>

                        <button type="submit" class="button-primary">Done &amp; Go to Dashboard <span>→</span></button>
                    </form>
                </section>

                <aside class="auth-media">
                    <img src="{{ asset('image/rafiki.png') }}" alt="Hobby Illustration" onerror="this.onerror=null;this.src='{{ asset('image/pana.png') }}';">
                </aside>
            </div>
        @endif
    </div>
</div>
@endsection