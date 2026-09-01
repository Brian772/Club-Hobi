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

<<<<<<< Updated upstream
                <aside class="auth-media">
                    <img src="{{ asset('image/amico1.png') }}" alt="Registration Illustration">
                </aside>
=======
        <div class="social-login">
          <a href="{{ route('social.redirect', 'google') }}" class="social-button">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
            <span>Continue With Google</span>
          </a>

          <a href="{{ route('social.redirect', 'facebook') }}" class="social-button">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook">
            <span>Continue With Facebook</span>
          </a>
        </div>
      </div>

      <div class=" flex order-1 justify-center md:order-2 md:w-1/2 max-w-[400px] w-full">
        <img src="{{ asset('images/amico1.png') }}" alt="Registration Illustration"
          class="w-[150px] md:w-full max-w-[400px]"
          onerror="this.onerror=null; this.src='{{ asset('images/pana.png') }}';">
      </div>
    </div>
  @elseif($step == 2)
    <div class="flex flex-col md:flex-row items-center justify-center">
      <div class="order-2 md:order-1 md:w-1/2 max-w-[400px] w-full flex flex-col">
        <div class="flex flex-col justify-center items-start mb-2">
          <h2 class="text-primary text-heading-2 font-bold md:text-heading-1">Tell us about yourself</h2>
          <p class="text-ink-muted text-caption">This information will appear on your profile within the club.</p>
        </div>

        <div class="register-progress" aria-label="Registration progress">
          <span class="progress-item"></span>
          <span class="progress-item active"></span>
          <span class="progress-item"></span>
        </div>

        @php
          $avatarPath = session('register.avatar_url');
          $avatarPreviewUrl = null;

          if ($avatarPath) {
              $avatarPreviewUrl = str_starts_with($avatarPath, 'http')
                  ? $avatarPath
                  : ((function () use ($avatarPath) {
                      /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                      $disk = \Illuminate\Support\Facades\Storage::disk('public');

                      return $disk->url($avatarPath);
                  })());
          }
        @endphp

        <form method="POST" action="{{ route('register.step2') }}" data-turbo="false" class="form profile-form"
          enctype="multipart/form-data">
          @csrf

          <div class="flex items-center justify-start mb-2 profile-upload">
            {{-- <input type="file" id="avatar_url" name="avatar_url" accept=".jpg,.jpeg,.png" hidden> --}}

            <div class="flex flex-row justify-center mb-2">
              <label for="avatar_url" class="w-[96px] h-[96px] cursor-pointer relative block">
                <span
                  class="upload-icon w-[96px] h-[96px] border border-solid rounded-full flex items-center justify-center overflow-hidden m-0"
                  id="avatar-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M4 7.5C4 6.67 4.67 6 5.5 6H8L9.2 4.5H14.8L16 6H18.5C19.33 6 20 6.67 20 7.5V17.5C20 18.33 19.33 19 18.5 19H5.5C4.67 19 4 18.33 4 17.5V7.5Z"
                      stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                    <circle cx="12" cy="12.5" r="3.2" stroke="currentColor" stroke-width="1.8" />
                  </svg>
                </span>

                <span id="avatar-preview-wrap"
                  class="{{ $avatarPreviewUrl ? '' : 'hidden' }} w-[96px] h-[96px] overflow-hidden m-0 rounded-full">
                  <img id="avatar-preview" src="{{ $avatarPreviewUrl }}" alt="{{ session('register.name') }}"
                    class="w-full h-full object-cover block">
                </span>

                <input type="file" id="avatar_url" name="avatar_url" accept="image/*" class="hidden">
              </label>
>>>>>>> Stashed changes
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