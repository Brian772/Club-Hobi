@extends('layouts.app')

@section('title', 'Orbii | Reset Password Baru')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endsection

@section('content')
<div class="flex items-center justify-center min-h-[80vh]" style="width: 100%; padding: 1rem;">
  <div style="width: 100%; max-width: 480px; padding: 2rem;" class="rounded-xl border border-hairline shadow-md bg-white mx-auto flex flex-col gap-6">
    
    <h1 class="text-title text-center lg:text-2xl font-semibold text-primary">Reset Password Baru</h1>
    
    <form method="POST" action="{{ route('password.store') }}" class="flex flex-col gap-4">
      @csrf

      <input type="hidden" name="token" value="{{ $token }}">

      <div>
        <x-input-label for="email" :value="__('Email')" class="text-primary font-medium" />
        <x-text-input id="email" class="block mt-1 w-full rounded-md border-hairline" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" class="mt-2 text-accent-red text-sm" />
      </div>

      <div>
        <label for="password" class="text-primary font-medium text-sm">Password Baru</label>
        <div class="relative flex items-center mt-1">
          <input type="password" id="password" name="password" class="block w-full rounded-md border-hairline pr-10 px-3 py-2 text-sm border shadow-sm focus:outline-none focus:ring-2 focus:ring-primary" required autocomplete="new-password">
          <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 text-neutral-500 hover:text-neutral-700 focus:outline-none cursor-pointer">
            <i class="bi bi-eye-slash text-lg"></i>
          </button>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-2 text-accent-red text-sm" />
      </div>

      <div>
        <label for="password_confirmation" class="text-primary font-medium text-sm">Konfirmasi Password Baru</label>
        <div class="relative flex items-center mt-1">
          <input type="password" id="password_confirmation" name="password_confirmation" class="block w-full rounded-md border-hairline pr-10 px-3 py-2 text-sm border shadow-sm focus:outline-none focus:ring-2 focus:ring-primary" required autocomplete="new-password">
          <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-3 text-neutral-500 hover:text-neutral-700 focus:outline-none cursor-pointer">
            <i class="bi bi-eye-slash text-lg"></i>
          </button>
        </div>
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-accent-red text-sm" />
      </div>

      <div class="mt-4">
        <button type="submit" style="width: 100%;" class="rounded-md bg-primary px-4 py-2.5 text-caption font-semibold text-white shadow-sm hover:bg-primary/80 transition-colors cursor-pointer">
          {{ __('Reset Password') }}
        </button>
      </div>
    </form>
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