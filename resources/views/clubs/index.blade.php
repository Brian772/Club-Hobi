@extends('layouts.app')

@section('title', 'Orbii | Clubs')

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
            <div x-anchor.noflip="$refs.button" x-transition.opacity @click.outside="MenuOpen = false"
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
      <section id="alreadyJoin" class="pb-4 border-b border-hairline overflow-hidden h-max">
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
              <div x-anchor.noflip="$refs.button" x-transition.opacity @click.outside="MenuOpen = false"
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
        <div class="flex flex-row gap-6 overflow-x-auto scrollbar-hide h-max pb-4">
          @foreach ($joinedClub as $club)
            <div
              class="flex flex-col items-stretch border min-w-75 w-100 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
              @if ($club->cover_url)
                {{-- <img src="{{ $club->cover_url }}" alt="{{ $club->name }}" class="w-full h-48 object-cover"> --}}
                <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="{{ $club->name }}"
                  loading="lazy" class="w-full h-48 rounded-t-lg object-cover">
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
                <div class="flex flex-row items-center justify-between mt-2">
                  <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
                  <p class="text-caption text-ink-muted flex flex-row gap-2 items-center">
                    @if ($club->visibility === 'public')
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-globe preview-icon">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                        <path d="M2 12h20" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-lock preview-icon">
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
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round"
                    class="lucide lucide-arrow-right preview-icon group-hover:translate-x-1 transition-transform duration-300">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                  </svg>
                </a>
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
            <div x-data="{ MenuOpen: false }" class="relative">
              <button type="button" x-ref="button" @click="MenuOpen = true"
                class="text-ink-muted text-body-mid rounded-full p-2 hover:bg-hairline">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" class="lucide lucide-ellipsis-vertical">
                  <circle cx="12" cy="12" r="1" />
                  <circle cx="12" cy="5" r="1" />
                  <circle cx="12" cy="19" r="1" />
                </svg>
              </button>

              <div x-show="MenuOpen" x-cloak @keydown.escape.window="MenuOpen = false">
                <div x-anchor.noflip="$refs.button" x-transition.opacity @click.outside="MenuOpen = false"
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
          <p class="text-caption text-ink-muted">Anda belum bergabung ke klub manapun.</p>
        </div>
      </section>
    @endif

    @if ($recomendedClubs->isNotEmpty())
      <section class="mt-8 pb-4 mb-12">
        <h2 class="text-title lg:text-heading-2 font-bold mb-2">Rekomendasi Klub</h2>
        <p class="text-caption text-ink-muted mb-8 line-clamp-1 font-semibold">Berdasarkan Minat :
          <span class="font-normal">{{ implode(', ', auth()->user()->interest_array ?? []) }}</span>
        </p>
        <div class="flex flex-row gap-4 xl:gap-6 flex-wrap">
          @foreach ($recomendedClubs as $club)
            <div
              class="flex flex-col items-stretch min-w-75 w-100 lg:w-75 border rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
              @if ($club->cover_url)
                <img src="{{ $club->cover_display_url }}" alt="{{ $club->name }}"
                  class="w-full h-48 rounded-t-lg object-cover" loading="lazy">
              @else
                <div class="w-full h-48 rounded-t-lg bg-gray-200 flex items-center justify-center">
                  <span class="text-ink-muted">Tidak ada gambar</span>
                </div>
              @endif

              <div class="p-4 flex flex-col flex-1">
                <h3 class="text-body-mid font-semibold mb-2 line-clamp-2 flex flex-row items-center justify-between">
                  {{ $club->name }}
                  <span
                    class="text-caption text-ink-muted">{{ $club->hobby->name ?? 'Kategori Tidak Diketahui' }}</span>
                </h3>
                <p class="text-caption text-ink-muted mb-2 line-clamp-2">{{ $club->description }}</p>
                <div class="flex flex-row items-center justify-between mt-2">
                  <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
                  <p class="text-caption text-ink-muted flex flex-row gap-2 items-center">
                    @if ($club->visibility === 'public')
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-globe preview-icon">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                        <path d="M2 12h20" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-lock preview-icon">
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
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round"
                    class="lucide lucide-arrow-right preview-icon group-hover:translate-x-1 transition-transform duration-300">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                  </svg>
                </a>
              </div>
            </div>
          @endforeach
        </div>
      </section>
    @else
      <section class="mt-8 pb-4 mb-12">
        <h2 class="text-title lg:text-heading-2 font-bold mb-2">Rekomendasi Klub</h2>
        <p class="text-caption text-ink-muted mb-8 line-clamp-1 font-semibold">Berdasarkan Minat :
          <span class="font-normal">{{ implode(', ', auth()->user()->interest_array ?? []) }}</span>
        </p>
        <div class="flex flex-col w-full h-max pt-12 justify-center items-center">
          <p class="text-title text-ink">Tidak ada rekomendasi klub</p>
          <span class="text-ink-muted text-body-mid">Tidak ada rekomendasi klub berdasarkan minat anda.</span>
        </div>
      </section>
    @endif
  @endif
@endsection