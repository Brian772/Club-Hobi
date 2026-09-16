@extends('layouts.app')

@section('content')
  @if ($isEmpty)
    <section class="flex flex-col h-full">
      <div class="flex flex-row justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Belum Ada Klub</h2>
        <div x-data="{ MenuOpen: false }" class="relative">
          <button type="button" x-ref="button" @click="MenuOpen = true"
            class="text-ink-muted text-body-mid rounded-full p-2 hover:bg-hairline">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-ellipsis-vertical">
              <circle cx="12" cy="12" r="1" />
              <circle cx="12" cy="5" r="1" />
              <circle cx="12" cy="19" r="1" />
            </svg>
          </button>

          <div x-show="MenuOpen" x-cloak @keydown.escape.window="MenuOpen = false">
            <div x-anchor.noflip="$refs.button" x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
              x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
              x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="MenuOpen = false"
              @click="MenuOpen = false"
              class="z-50 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
              <a href="{{ route('clubs.request') }}"
                class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                Ajukan Klub Baru
              </a>
              <a href="{{ route('clubs.request.list') }}"
                class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                Pengajuan Saya
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="flex flex-col w-full h-full justify-center items-center">
        <p class="text-title text-ink">Tidak ada klub saat ini</p>
        <span class="text-ink-muted text-body-mid">Buat klub pertama anda</span>
        <a href="{{ route('clubs.request') }}"
          class="text-primary bg-primary/10 hover:text-white mt-8 hover:bg-primary rounded-md px-4 py-2">+ Ajukan Klub
          Baru</a>
      </div>
    </section>
  @else
    @if ($joinedClub->isNotEmpty())
      <section id="alreadyJoin" class="pb-4 border-b border-hairline">
        <div class="flex flex-row  justify-between items-center mb-4">
          <h2 class="text-title lg:text-heading-2 font-bold">Klub Anda</h2>
          <div x-data="{ MenuOpen: false }" class="relative">
            <button type="button" x-ref="button" @click="MenuOpen = true"
              class="text-ink-muted text-body-mid rounded-full p-2 hover:bg-hairline">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-ellipsis-vertical">
                <circle cx="12" cy="12" r="1" />
                <circle cx="12" cy="5" r="1" />
                <circle cx="12" cy="19" r="1" />
              </svg>
            </button>

            <div x-show="MenuOpen" x-cloak @keydown.escape.window="MenuOpen = false">
              <div x-anchor.noflip="$refs.button" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="MenuOpen = false"
                @click="MenuOpen = false"
                class="z-50 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
                <a href="{{ route('clubs.request') }}"
                  class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                  Ajukan Klub Baru
                </a>
                <a href="{{ route('clubs.request.list') }}"
                  class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                  Pengajuan Saya
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 2xl:grid-cols-4"
          :class="notifOpen ? 'lg:grid-cols-1 xl:grid-cols-2' : 'lg:grid-cols-3 xl:grid-cols-3'">
          @foreach ($joinedClub as $club)
            <div
              class="flex flex-col h-full border rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
              @if ($club->cover_url)
                {{-- <img src="{{ $club->cover_url }}" alt="{{ $club->name }}" class="w-full h-48 object-cover"> --}}
                <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="{{ $club->name }}"
                  loading="lazy" class="w-full h-48 rounded-t-lg object-cover">
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
          <div class="flex flex-row justify-between items-center mb-4">
            <h2 class="text-title lg:text-heading-2 font-bold mb-4">Klub Anda</h2>
            <nav>
              <a href="{{ route('clubs.request') }}" class="text-primary hover:text-primary-active">+ Ajukan Klub
                Baru</a>
              <span class="text-ink-muted mx-2">|</span>
              <a href="{{ route('clubs.request.list') }}" class="text-primary hover:text-primary-active">Pengajuan
                Saya</a>
            </nav>
          </div>
          <p class="text-caption text-ink-muted">Anda belum bergabung ke klub manapun.</p>
        </div>
      </section>
    @endif

    @if ($recomendedClubs->isNotEmpty())
      <section class="mt-8 pb-4mb-12">
        <h2 class="text-title lg:text-heading-2 font-bold mb-4">Rekomendasi Klub</h2>
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
                  class="w-full h-48 rounded-t-lg object-cover" loading="lazy">
              @endif

              <div class="p-4 flex flex-col flex-1">
                <span class="text-caption text-ink-muted">{{ $club->hobby->name ?? 'Kategori Tidak Diketahui' }}</span>
                <h3 class="text-lg font-semibold mb-2 line-clamp-2">{{ $club->name }}</h3>
                <p class="text-caption text-ink-muted mb-2 line-clamp-2">{{ $club->description }}</p>
                <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
              </div>
              <div class="mt-auto w-full p-2">
                <?php
                $isAlreadyRequested = $club
                    ->joinRequests()
                    ->where('user_id', auth()->id())
                    ->where('status', 'pending')
                    ->exists();
                
                $pendingRequest = $pendingRequests->get($club->id);
                $isAlreadyRequested = $pendingRequest !== null;
                ?>
                @if ($isAlreadyRequested)
                  <div class="flex flex-row gap-2">
                    <button
                      class="w-full border border-hairline bg-canvas-soft rounded-md text-primary py-2 cursor-not-allowed"
                      type="button" disabled>
                      Menunggu Persetujuan
                    </button>
                    <form action="{{ route('clubs.join.request.cancel', [$club->id, $pendingRequest->id]) }}"
                      method="POST">
                      @csrf
                      @method('DELETE')
                      <button
                        class="w-max bg-gray-200 text-ink hover:bg-gray-300 py-2 px-4 rounded-md text-center cursor-pointer"
                        type="submit">
                        Batal
                      </button>
                    </form>
                  </div>
                @else
                  <form action="{{ route('clubs.join.request', $club->id) }}" method="POST">
                    @csrf
                    <button class="w-full bg-primary rounded-md text-white py-2 cursor-pointer" type="submit">
                      Permintaan Bergabung
                    </button>
                  </form>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </section>
    @endif
  @endif
@endsection
