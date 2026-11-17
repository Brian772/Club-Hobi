@extends('layouts.app')

@section('title', 'Orbii | Reset Password')

@section('content')
<div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md lg:max-w-lg p-6 rounded-xl border border-hairline shadow-md bg-white mx-auto flex flex-col gap-4">
        
        <img src="{{ asset('images/email.png') }}" alt="Email" class="w-max h-32 lg:h-64 mx-auto object-contain">
        
        <h1 class="text-title text-center lg:text-2xl font-semibold text-primary">Reset Password</h1>
        
        <div class="text-caption text-ink-muted text-center">
            {{ __('Masukkan email Anda untuk mendapatkan tautan reset password.') }}
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-primary font-medium" />
                <x-text-input id="email" class="block mt-1 w-full rounded-md border-hairline" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-accent-red text-sm" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-caption font-semibold text-white shadow-sm hover:bg-primary/80 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                    {{ __('Kirim Tautan Reset Password') }}
                </button>

                <a class="text-caption text-red-600 hover:text-red-700 underline font-semibold cursor-pointer" href="{{ route('login') }}">
                    {{ __('Kembali') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection