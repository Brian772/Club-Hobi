@extends('layouts.app')

@section('content')
  @if ($isEmpty)
    <section>
      <h2 class="text-heading-1 font-bold mb-4">Tidak Ada Klub</h2>
      <p class="text-caption text-ink-muted">Belum ada klub yang tersedia saat ini. Silakan cek kembali nanti.</p>
    </section>
  @else
    @if ($joinedClub->isNotEmpty())
      <section id="alreadyJoin" class="pb-4 border-b border-hairline">
        <div class="flex flex-row  justify-between items-center mb-4">
          <h2 class="text-2xl font-bold">Klub Anda</h2>
          <nav>
            <a href="{{ route('clubs.request') }}" class="text-primary hover:text-primary-active">+ Ajukan Klub Baru</a>
            <span class="text-ink-muted mx-2">|</span>
            <a href="{{ route('clubs.request.list') }}" class="text-primary hover:text-primary-active">Pengajuan Saya</a>
          </nav>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 2xl:grid-cols-4"
          :class="notifOpen ? 'lg:grid-cols-1 xl:grid-cols-2' : 'lg:grid-cols-3 xl:grid-cols-3'">
          @foreach ($joinedClub as $club)
            <div
              class="flex flex-col h-full border rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
              @if ($club->cover_url)
                {{-- <img src="{{ $club->cover_url }}" alt="{{ $club->name }}" class="w-full h-48 object-cover"> --}}
                <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="{{ $club->name }}"
                  class="w-full h-48 rounded-t-lg object-cover">
              @endif

              <div class="p-4 flex flex-col flex-1">
                <span class="text-caption text-ink-muted">{{ $club->hobby->name ?? 'Kategori Tidak Diketahui' }}</span>
                <h3 class="text-lg font-semibold mb-2 line-clamp-2">{{ $club->name }}</h3>
                <p class="text-caption text-ink-muted mb-2 line-clamp-2">{{ $club->description }}</p>
                <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
              </div>
              <div class="mt-auto w-full p-2">
                <a href="{{ route('clubs.show', $club->id) }}"
                  class="bg-primary rounded-md text-white py-2 w-full flex items-center justify-center">Lihat Klub</a>
              </div>
            </div>
          @endforeach
        </div>
      </section>
    @else
      <section id="alreadyJoin" class="border-b border-hairline">
        <div class="mb-12">
          <h2 class="text-2xl font-bold mb-4">Klub Anda</h2>
          <p class="text-caption text-ink-muted">Anda belum bergabung ke klub manapun.</p>
          <p></p>
        </div>
      </section>
    @endif

    @if ($recomendedClubs->isNotEmpty())
      <section class="mt-8 pb-4mb-12">
        <h2 class="text-2xl font-bold mb-4">Rekomendasi Klub</h2>
        <p class="text-caption text-ink-muted mb-2">Berdasarkan Minat:
          {{ implode(', ', auth()->user()->interest_array ?? []) }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 2xl:grid-cols-4"
          :class="notifOpen ? 'lg:grid-cols-1 xl:grid-cols-2' : 'lg:grid-cols-3 xl:grid-cols-3'">
          @foreach ($recomendedClubs as $club)
            <div
              class="flex flex-col h-full border rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
              @if ($club->cover_url)
                {{-- <img src="{{ $club->cover_url }}" alt="{{ $club->name }}" class="w-full h-48 object-cover"> --}}
                <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="{{ $club->name }}"
                  class="w-full h-48 rounded-t-lg object-cover">
              @endif

              <div class="p-4 flex flex-col flex-1">
                <span class="text-caption text-ink-muted">{{ $club->hobby->name ?? 'Kategori Tidak Diketahui' }}</span>
                <h3 class="text-lg font-semibold mb-2 line-clamp-2">{{ $club->name }}</h3>
                <p class="text-caption text-ink-muted mb-2 line-clamp-2">{{ $club->description }}</p>
                <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
              </div>
              <div class="mt-auto w-full p-2">
                <form action="{{ route('clubs.join', $club->id) }}" method="POST">
                  @csrf
                  <button class="w-full bg-primary rounded-md text-white py-2 cursor-pointer"
                    type="submit">Bergabung</button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      </section>
    @endif
  @endif
@endsection
