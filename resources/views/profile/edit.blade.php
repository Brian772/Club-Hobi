@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="max-w-xl mx-auto py-8">
    <div class="mb-6">
      <h1 class="text-3xl font-extrabold text-gray-900">Edit Profil</h1>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-5 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
      @csrf
      @method('PATCH')

      <div>
        <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-primary focus:outline-none">
        @error('name')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-primary focus:outline-none">
        @error('email')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div class="flex justify-end">
        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary/90">
          Simpan
        </button>
      </div>
    </form>
  </div>
@endsection
