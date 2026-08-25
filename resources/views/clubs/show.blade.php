@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <a href="{{ route('clubs.index') }}" class="flex items-center gap-2 text-ink-muted mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
      <path d="M0 0h16v9H0z" fill="none" />
      <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
      <path fill="currentColor"
        d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
    </svg>
  </a>
  <header class="flex flex-col justify-center items-start md:items-stretch md:flex-row w-full border-b border-hairlinep pb-4">
    <img src="https://picsum.photos/seed/{{ $club->id }}/400/300" alt="Logo {{ $club->name }}"
      class="rounded-md w-full md:w-100 h-48 md:h-auto object-cover mb-4 md:mb-0 md:mr-4 border border-hairline">
    <div class="flex flex-col justify-between items-start w-full self-stretch">
      <div class="flex flex-col gap-2">
        <div class="flex flex-col gap-2 mb-2">
          <h1 class="text-heading-2 text-ink font-bold">Klub {{ $club->name }}</h1>
          <span class="text-caption lg:text-body-mid text-ink-muted">{{ $club->category }} <span
              class="font-extrabold">·</span>
            {{ $club->members_count }} anggota</span>
        </div>
        <p class="text-caption lg:text-body-mid text-ink-muted">{{ $club->description }}</p>
      </div>
      <div class="mt-2 flex flex-row gap-2 items-center justify-start w-full">
        <form action="{{ route('clubs.leave', $club->id) }}" method="POST">
          @csrf
          @method('DELETE')
          <x-secondary-button class="w-max px-4" type="submit">
            Keluar Klub
          </x-secondary-button>
        </form>

        @can('admin')
          <x-secondary-button class="w-max px-4" type="button" onclick="window.location='{{ route('clubs.edit', $club->id) }}'">
            Edit Klub
          </x-secondary-button>
        @endcan
      </div>
    </div>
  </header>

  <section id="postingan" class="pt-4">
    <header class="mb-4">
      <h2 class="text-heading-2 text-ink font-bold">Postingan</h2>
    </header>

    <main class="flex flex-col gap-4">
      @if ($club->posts->isEmpty())
        <p class="text-caption text-ink-muted">Belum ada postingan di klub ini.</p>
      @else
        @foreach ($posts as $post)
          <x-post :post="$post" />
        @endforeach
      @endif
    </main>
  </section>
@endsection
