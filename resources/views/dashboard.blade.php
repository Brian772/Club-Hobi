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
    <h2 class="text-lg font-bold text-neutral-900">Club yang anda ikuti</h2>
    <a href="{{ route('clubs.index') }}" class="text-sm text-neutral-500 hover:text-neutral-800">Lihat selengkapnya →</a>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    @if ($joinedClub->isNotEmpty())
      @foreach ($joinedClub as $club)
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
          <img src="{{ $club->cover_url }}" alt="{{ $club->name }}" class="w-full h-32 object-cover">
          <div class="p-4">
            <span class="text-caption text-ink-muted">{{ $club->category ?? 'Kategori Tidak Diketahui' }}</span>
                  <h3 class="text-lg font-semibold mb-2">{{ $club->name }}</h3>
                  <p class="text-caption text-ink-muted mb-2">{{ $club->description }}</p>
                  <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
          </div>
        </div>
      @endforeach
    @else
      <section id="alreadyJoin" class="border-b border-hairline flex w-full">
        <div class="mb-12 flex justify-center w-full">
          <p class="text-caption text-ink-muted">Anda belum bergabung ke klub manapun.</p>
          <p></p>
        </div>
      </section>
    @endif
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
