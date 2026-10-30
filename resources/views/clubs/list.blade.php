@extends('layouts.app')

@section('title', 'Orbii | Klub Saya')

@section('content')
  <div class="flex flex-col gap-4">
    <header class="flex flex-row gap-2 lg:gap-4 items-center justify-start mb-6">
      <a href="{{ route('clubs.index') }}" class="text-ink-muted">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
          <path d="M0 0h16v9H0z" fill="none" />
          <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
          <path fill="currentColor"
            d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
        </svg>
      </a>
      <h2 class="text-title lg:text-heading-2 text-ink-secondary">Klub Saya</h2>
    </header>
    <div class="flex flex-col gap-4">
      @forelse ($userClubs as $club)
        <div
          class="flex flex-col items-stretch min-w-75 w-100 lg:w-75 border rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
          @if ($club->cover_url)
            <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="{{ $club->name }}"
              class="w-full h-48 rounded-t-lg object-cover" draggable="false" loading="lazy">
          @else
            <div class="w-full h-48 rounded-t-lg bg-gray-200 flex items-center justify-center">
              <span class="text-ink-muted">Tidak ada gambar</span>
            </div>
          @endif

          <div class="p-4 flex flex-col flex-1">
            <h3 class="text-body-mid font-semibold mb-2 line-clamp-2 flex flex-row items-center justify-between">
              {{ $club->name }}
              <span class="text-caption text-ink-muted">{{ $club->hobby->name ?? 'Kategori Tidak Diketahui' }}</span>
            </h3>
            <p class="text-caption text-ink-muted mb-2 line-clamp-2">{{ $club->description }}</p>
            <div class="flex flex-row items-center justify-between mt-auto">
              <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
              <p class="text-caption text-ink-muted flex flex-row gap-2 items-center">
                @if ($club->visibility === 'public')
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-globe preview-icon">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                    <path d="M2 12h20" />
                  </svg>
                @else
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-lock preview-icon">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                  </svg>
                @endif
                {{ $club->visibility }}
              </p>
            </div>
          </div>
          <div class="mt-auto w-full p-2">
            <a href="{{ route('clubs.show', $club->id) }}"
              class="group bg-primary rounded-md gap-2 text-white py-2 w-full flex flex-row items-center justify-center">
              Lihat Klub
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-arrow-right preview-icon group-hover:translate-x-1 transition-transform duration-300">
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>
      @empty
        <div class="flex flex-col h-full justify-center items-center">
      <h2 class="text-title text-ink">Tidak ada klub</h2>
      <p class="text-body-mid text-ink-muted">Anda belum memiliki klub apa pun.</p>
      <a href="{{ route('clubs.request') }}"
        class="text-primary bg-primary/10 hover:text-white mt-8 hover:bg-primary rounded-md px-4 py-2">+ Ajukan Klub
        Baru</a>
    </div>
      @endforelse
    </div>
  </div>
@endsection
