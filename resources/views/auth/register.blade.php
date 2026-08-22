@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')

  @if ($step == 1)
    <div class="flex flex-col md:flex-row items-center justify-center">
      <div class="order-2 md:order-1 md:w-1/2 max-w-[400px] w-full flex flex-col">
        <div class="flex flex-col w-full justify-center items-start my-2">
          <h2 class="text-primary text-heading-2 font-bold md:text-heading-1">Join the orbii</h2>
          <p class="text-ink-muted text-caption">Start finding a hobby club that's right for you.</p>
        </div>

        <div class="register-progress" aria-label="Registration progress">
          <span class="progress-item active"></span>
          <span class="progress-item"></span>
          <span class="progress-item"></span>
        </div>

        <form method="POST" action="{{ route('register.step1') }}" class="form">
          @csrf

          <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <input type="email" id="email" name="email" value="{{ old('email') }}"
              placeholder="EmailAnda@example.com" required autofocus>
            @error('email')
              <small class="error-text">{{ $message }}</small>
            @enderror
          </div>

          <div class="form-group">
            <x-input-label for="password" :value="__('Password')" />
            <input type="password" id="password" name="password" placeholder="********" required>
            @error('password')
              <small class="error-text">{{ $message }}</small>
            @enderror
          </div>

          <div class="form-group">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********"
              required>
          </div>

          <div class="remember-me my-2 mt-[24px]">
            <label>
              <input type="checkbox" name="remember" class="rounded-sm border-ink-faint">
              <span class="text-ink-muted text-caption">I agree to the Terms &amp; Conditions and Privacy Policy</span>
            </label>
          </div>

          <x-secondary-button type="submit">
            Next
          </x-secondary-button>
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

        <form method="POST" action="{{ route('register.step2') }}" class="form profile-form"
          enctype="multipart/form-data">
          @csrf

          <div class="flex items-center justify-start mb-2 profile-upload">
            <input type="file" id="avatar_url" name="avatar_url" accept=".jpg,.jpeg,.png" hidden>

            <div class="flex flex-row justify-center mb-2">
              <label for="avatar_url" class="" style="cursor:pointer;">
                <span class="upload-icon" id="avatar-icon" aria-hidden="true"
                  style="width:96px; height:96px; border-radius:50%; display:flex; align-items:center; justify-content:center; overflow:hidden; margin:0 auto;">
                  <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M4 7.5C4 6.67 4.67 6 5.5 6H8L9.2 4.5H14.8L16 6H18.5C19.33 6 20 6.67 20 7.5V17.5C20 18.33 19.33 19 18.5 19H5.5C4.67 19 4 18.33 4 17.5V7.5Z"
                      stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                    <circle cx="12" cy="12.5" r="3.2" stroke="currentColor" stroke-width="1.8" />
                  </svg>
                </span>

                <span id="avatar-preview-wrap"
                  style="display:none; width:96px; height:96px; border-radius:50%; overflow:hidden; margin:0 auto;">
                  <img id="avatar-preview" alt=""
                    style="width:100%; height:100%; object-fit:cover; display:block;">
                </span>
            </div>

            </label>
            <span class="upload-copy ml-2 justify-center items-start" id="avatar-copy">
              <strong>Upload Photo Profile</strong>
              <small>Choose File · JPG/PNG, max 5MB</small>
            </span>
            @error('avatar_url')
              <small class="error-text">{{ $message }}</small>
            @enderror
          </div>

          <div class="form-group">
            <x-input-label for="name" :value="__('Full Name')" />
            <input type="text" id="name" name="name" class="rounded-md border-outline"
              value="{{ old('name') }}" placeholder="@username" required>
            @error('name')
              <small class="error-text">{{ $message }}</small>
            @enderror
          </div>

          <div class="form-group">
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio" name="bio" placeholder="Tell us a little about yourself..."
              class="rounded-md border-outline">{{ old('bio') }}</textarea>
            @error('bio')
              <small class="error-text">{{ $message }}</small>
            @enderror
          </div>

          <div class="flex flex-col justify-center mt-2 items-center">
            <x-secondary-button type="submit">
              Next
            </x-secondary-button>

            <button type="submit" class="" formnovalidate
              onclick="document.getElementById('avatar_url').value = ''; document.getElementById('avatar-preview-wrap').style.display = 'none'; document.getElementById('avatar-icon').style.display = 'flex'; document.getElementById('avatar-copy').style.display = '';"
              style="background:none; border:none; padding:0; color:#9a9a9a; cursor:pointer; font:inherit; text-decoration:underline;">
              Skip For Now
            </button>
          </div>
        </form>

        <script>
          document.getElementById('avatar_url').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const preview = document.getElementById('avatar-preview');
            const previewWrap = document.getElementById('avatar-preview-wrap');
            const icon = document.getElementById('avatar-icon');
            const copy = document.getElementById('avatar-copy');

            const reader = new FileReader();
            reader.onload = function(ev) {
              preview.src = ev.target.result;
              previewWrap.style.display = 'block';
              icon.style.display = 'none';
              copy.style.display = 'none';
            };
            reader.readAsDataURL(file);
          });
        </script>
      </div>

      <div class="flex order-1 justify-center md:order-2 md:w-1/2 max-w-[400px] w-full">
        <img src="{{ asset('images/amico2.png') }}" alt="Profile Illustration"
          class="w-[150px] md:w-full max-w-[400px]"
          onerror="this.onerror=null; this.src='{{ asset('images/pana.png') }}';">
      </div>
    </div>
  @elseif($step == 3)
    <div class="flex flex-col md:flex-row items-center justify-center">
      <div class="order-2 md:order-1 md:w-1/2 max-w-[400px] w-full flex flex-col">
        <div class="flex flex-col justify-center items-start mb-2">
          <h2 class="text-primary text-heading-2 md:text-heading-1 font-bold">Choose your hobby</h2>
          <p class="text-ink-muted text-caption">We'll recommend clubs based on your interests.</p>
        </div>

        <div class="register-progress" aria-label="Registration progress">
          <span class="progress-item"></span>
          <span class="progress-item"></span>
          <span class="progress-item active"></span>
        </div>

        <form method="POST" action="{{ route('register.step3') }}" class="form hobby-form">
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

          <x-secondary-button type="submit">
            Done &amp; Go to Dashboard <span class="button-arrow">→</span>
          </x-secondary-button>
        </form>
      </div>

      <div class="order-1 md:order-2 md:w-1/2 max-w-[400px] w-full flex justify-center items-center">
        <img src="{{ asset('images/rafiki.png') }}" alt="Hobby Illustration" class="w-[150px] md:w-full max-w-[400px]"
          onerror="this.onerror=null; this.src='{{ asset('images/pana.png') }}';">
      </div>
    </div>
  @endif

@endsection
