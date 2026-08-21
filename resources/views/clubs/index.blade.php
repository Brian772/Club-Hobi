@extends('layouts.app')

@section('content')
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    @if (session('success'))
      <div class="bg-accent-green/40 border-accent-green text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
      </div>
    @endif

    @if ($isEmpty)
      <section>
        <h2 class="text-heading-1 font-bold mb-4">Tidak Ada Klub</h2>
        <p class="text-caption text-ink-muted">Belum ada klub yang tersedia saat ini. Silakan cek kembali nanti.</p>
      </section>
    @else
      @if ($recomendedClubs->isNotEmpty())
        <section>
          <h2 class="text-2xl font-bold mb-4">Rekomendasi Klub</h2>
          <p class="text-caption text-ink-muted">Berdasarkan Minat:
            {{ implode(', ', auth()->user()->interest_array ?? []) }}</p>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
            :class="notifOpen ? 'lg:grid-cols-3' : 'lg:grid-cols-4'">
            @foreach ($recomendedClubs as $club)
              <a href="{{ route('clubs.show', $club->id) }}"
                class="block border rounded-lg overvlow-hidden hover:shadow-lg transition-shadow duration-300">
                @if ($club->cover_url)
                  <img src="{{ $club->cover_url }}" alt="{{ $club->name }}" class="w-full h-48 object-cover">
                @endif

                <div class="p-4">
                  <span class="text-caption text-ink-muted">{{ $club->category ?? 'Kategori Tidak Diketahui' }}</span>
                  <h3 class="text-lg font-semibold mb-2">{{ $club->name }}</h3>
                  <p class="text-caption text-ink-muted mb-2">{{ $club->description }}</p>
                  <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
                </div>
              </a>
            @endforeach
          </div>
        </section>
      @endif

      <section>
        <h2 class="text-2xl font-bold mb-4">Semua Klub</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
          :class="notifOpen ? 'lg:grid-cols-3' : 'lg:grid-cols-4'">
          @foreach ($clubs as $club)
            <a href="{{ route('clubs.show', $club->id) }}"
              class="block border rounded-lg overvlow-hidden hover:shadow-lg transition-shadow duration-300">
              @if ($club->cover_url)
                <img src="{{ $club->cover_url }}" alt="{{ $club->name }}" class="w-full h-48 object-cover">
              @endif

              <div class="p-4">
                <span class="text-caption text-ink-muted">{{ $club->category ?? 'Kategori Tidak Diketahui' }}</span>
                <h3 class="text-lg font-semibold mb-2">{{ $club->name }}</h3>
                <p class="text-caption text-ink-muted mb-2">{{ $club->description }}</p>
                <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
              </div>
            </a>
          @endforeach
        </div>

        <div class="mt-6">
          {{ $clubs->links() }}
        </div>
      </section>

    @endif
  </div>
@endsection
