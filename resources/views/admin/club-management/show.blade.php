@extends('layouts.app')

@section('content')
  <header class="flex flex-row gap-2 lg:gap-4 items-center justify-start mb-6">
    <a href="{{ route('admin.club-management') }}" class="text-ink-muted">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
        <path d="M0 0h16v9H0z" fill="none" />
        <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
        <path fill="currentColor"
          d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
      </svg>
    </a>
    <h2 class="text-title lg:text-heading-2 flex flex-row items-center gap-2 justify-center text-ink">Club
      {{ $clubs->name }} Details</h2>
    </h2>
  </header>

  <main x-data="{ openDelete: false }">
    <section class="flex flex-col gap-2 p-2 lg:p-4 rounded-lg border border-hairline">
      <img src="{{ $clubs->cover_url ? Storage::url($clubs->cover_url) : '' }}" alt="{{ $clubs->name }}" width="256"
        height="128" class="rounded-md object-cover">
      <div class="flex flex-col gap-1">
        <h3 class="text-title text-ink flex flex-row items-center gap-2">{{ $clubs->name }}</h3>
        <span class="text-body-mid font-semibold">{{ $clubs->hobby->name }}</span>
        <div class="flex flex-row gap-2 items-center">
          <span class="text-body-mid font-semibold text-ink-muted">Owner:</span>
          <span class="text-body text-ink">{{ $clubs->creator->name }}</span>
        </div>
        <p class="text-body-mid text-ink-muted mt-4">{{ $clubs->description }}</p>
      </div>
    </section>

    <section class="grid grid-cols-2 lg:grid-cols-3 gap-2 lg:gap-4 items-center justify-start mt-4">
      <div class="flex flex-col items-start justify-start border border-hairline rounded-lg p-4">
        <span class="text-body-mid font-semibold text-ink-muted">Members</span>
        <span class="text-2xl ml-4 font-bold">{{ $clubs->members->count() }}</span>
      </div>
      <div class="flex flex-col items-start justify-start border border-hairline rounded-lg p-4">
        <span class="text-body-mid font-semibold text-ink-muted">Moderator</span>
        <span class="text-2xl ml-4 font-bold">{{ $clubs->members->where('role', 'moderator')->count() }}</span>
      </div>
    </section>

    <section class="flex flex-col gap-2 p-2 lg:p-4 rounded-lg border border-hairline mt-4">
      <h3 class="text-title text-ink">More Details</h3>
      <div class="mt-4">
        <div class="flex flex-col gap-2">
          <div class="flex flex-row gap-2 items-center">
            <span class="text-body-mid font-semibold text-ink-muted">Club ID:</span>
            <span class="text-body text-ink">{{ $clubs->id }}</span>
          </div>
        </div>
        <div class="flex flex-row gap-2 items-center">
          <span class="text-body-mid font-semibold text-ink-muted">Created At:</span>
          <span class="text-body text-ink">{{ $clubs->created_at }}</span>
        </div>
      </div>
    </section>

    <section class="mt-12">
      <header class="w-full flex flex-col mb-4">
        <h3 class="text-title lg:text-heading-3 text-ink">Danger Zone</h3>
      </header>

      <main class="flex flex-col gap-2 p-4 border border-accent-red rounded-lg">
        <div class="flex flex-row items-center justify-between ">
          <div class="flex flex-col gap-1">
            <h3 class="text-title text-ink">Delete Club</h3>
            <p class="text-body-mid text-ink-muted">Delete {{ $clubs->name }}. If you delete this club, you will not be
              able to recover it.</p>
          </div>
          <button type="button" @click="openDelete = true"
            class="px-4 py-2 rounded-md text-accent-red hover:underline hover:underline-offset-2">
            Delete
          </button>
        </div>
      </main>
    </section>

    {{-- Delete Modal --}}
    <div x-show="openDelete" x-cloak @keydown.escape.window="openDelete = false"
      class="fixed flex items-center justify-center inset-0 z-50">
      <div @click="openDelete = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
      </div>
      <div class="fixed p-6 h-max rounded-lg z-50 w-max bg-canvas border border-hairline overflow-hidden max-w-md">
        <div class="flex flex-col gap-2 mb-4">
          <h3 class="text-title mb-4 text-ink">
            Delete {{ $clubs->name }}?
          </h3>
          <p class="text-body-mid mb-4 text-ink">Apakah Anda yakin ingin menghapus club ini? Semua data dan konten di
            dalamnya akan dihapus secara permanen dan tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="flex flex-row justify-end gap-2">
          <form action="{{ route('clubs.delete', $clubs->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
              class="flex flex-row gap-2 items-center px-4 py-2 text-caption bg-accent-red/10 rounded w-max text-accent-red hover:text-white hover:bg-accent-red">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-trash">
                <path d="M10 11v6" />
                <path d="M14 11v6" />
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                <path d="M3 6h18" />
                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
              </svg>
              Delete
            </button>
          </form>
          <button type="button"
            class="bg-gray-200 border border-hairline rounded text-caption px-4 py-2 text-ink hover:bg-gray-300"
            @click="openDelete = false">Cancel</button>
        </div>
      </div>
    </div>
  </main>
@endsection
