@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-neutral-900">Halo, {{ auth()->user()->name }} </h1>
    <a href="{{ route('posts.create') }}"
      class="hidden lg:inline-flex items-center gap-2 bg-primary text-white text-sm font-semibold px-5 py-2.5 rounded-md hover:bg-primary-active">
      + Buat Postingan
    </a>
  </div>

  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-neutral-900">Club yang anda ikuti</h2>
    <a href="{{ route('clubs.index') }}" class="text-sm text-neutral-500 hover:text-neutral-800">Lihat selengkapnya →</a>
  </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8 border-b border-hairline pb-4">
    @if ($joinedClub->isNotEmpty())
      @foreach ($joinedClub as $club)
        <a href="{{ route('clubs.show', $club->id) }}"
          class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
          {{-- <img src="{{ $club->cover_url }}" alt="{{ $club->name }}" class="w-full h-32 object-cover"> --}}
          <img src="https://picsum.photos/seed/{{ $club->id }}/400/300" alt="{{ $club->name }}"
            class="w-full h-48 object-cover">
          <div class="p-4">
            <span class="text-caption text-ink-muted">{{ $club->category ?? 'Kategori Tidak Diketahui' }}</span>
            <h3 class="text-lg font-semibold mb-2">{{ $club->name }}</h3>
            <p class="text-caption text-ink-muted mb-2 line-clamp-2">{{ $club->description }}</p>
            <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
          </div>
        </a>
      @endforeach
    @else
      <section id="alreadyJoin" class="flex w-full">
        <div class="mb-12 flex justify-center w-full">
          <p class="text-caption text-ink-muted">Anda belum bergabung ke klub manapun.</p>
        </div>
      </section>
    @endif
  </div>

  <div class="space-y-4">
    @foreach ($feedPosts as $post)
      @if ($post->is_announcement)
        <x-post-announcement :post="$post" />
      @else
        <section class="block lg:hidden">
          <x-post-mobile :post="$post" />
        </section>
        <section class="hidden lg:block">
          <x-post :post="$post" />
        </section>
      @endif
    @endforeach
  </div>
@endsection
