@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-neutral-900">Halo, {{ auth()->user()->name }} </h1>
    <a href="{{ route('posts.create') }}"
      class="hidden lg:inline-flex items-center gap-2 bg-blue-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-blue-700">
      + Buat Postingan
    </a>
  </div>

  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-neutral-900">hobi yang anda minati</h2>
    <a href="{{ route('clubs.index') }}" class="text-sm text-neutral-500 hover:text-neutral-800">Lihat selengkapnya →</a>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    @foreach ($recommendedClubs as $club)
      <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
        <img src="{{ $club->cover_image_url }}" alt="{{ $club->name }}" class="w-full h-32 object-cover">
        <div class="p-4">
          <h3 class="font-bold text-neutral-900">{{ $club->name }}</h3>
          <p class="text-sm text-neutral-500">{{ $club->category }} · {{ $club->members_count }} anggota</p>
          <p class="text-xs text-emerald-600 mt-2">● {{ $club->latest_activity_label }}</p>
        </div>
      </div>
    @endforeach
  </div>

  <div class="space-y-4">
    @foreach ($feedPosts as $post)
      <div class="bg-white rounded-xl border border-neutral-200 p-5">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-3">
            <img src="{{ $post->author->avatar_full_url ?? asset('images/default-avatar.png') }}"
              class="w-9 h-9 rounded-full object-cover" alt="{{ $post->author->name }}">
            <div>
              <span class="text-sm font-semibold text-neutral-900">{{ $post->author->name }}</span>
              <span class="text-sm text-neutral-400">memposting di {{ $post->club->name }}</span>
            </div>
          </div>
          <span class="text-xs text-neutral-400">{{ $post->created_at->diffForHumans() }}</span>
        </div>
        <p class="text-neutral-800 mb-3">{{ $post->body }}</p>
        @if ($post->image_url)
          <img src="{{ $post->image_url }}" class="w-full rounded-lg mb-3" alt="">
        @endif
      </div>
    @endforeach
  </div>
@endsection
