@extends('layouts.app')

@section('content')
  <header class="flex flex-row gap-2 lg:gap-4 items-center justify-start mb-6">
    <a href="{{ route('admin.clubs.request') }}" class="text-ink-muted">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
        <path d="M0 0h16v9H0z" fill="none" />
        <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
        <path fill="currentColor"
          d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
      </svg>
    </a>
    <h2 class="text-heading-2 flex flex-row items-center gap-2 justify-center text-ink">Club Request
      @if ($clubRequest->status === 'pending')
        <span
          class="rounded-full bg-accent-yellow/10 text-accent-yellow text-overline px-2 py-1">{{ Str::upper($clubRequest->status) }}</span>
      @elseif ($clubRequest->status === 'approved')
        <span
          class="rounded-full bg-accent-green/10 text-accent-green text-overline px-2 py-1">{{ Str::upper($clubRequest->status) }}</span>
      @else
        <span
          class="rounded-full bg-accent-red/10 text-accent-red text-overline px-2 py-1">{{ Str::upper($clubRequest->status) }}</span>
      @endif
    </h2>
  </header>

  <main x-data="{ rejectOpen: false }" class="mt-6 max-w-4xl">
    <form action="{{ route('admin.clubs.request.accept', $clubRequest->id) }}" method="POST" class="flex flex-col gap-4">
      @csrf
      @method('patch')
      <img src="{{ Storage::url($clubRequest->cover_url) }}" alt="{{ $clubRequest->name }} Cover"
        class="w-full lg:w-lg h-40 lg:h-64 rounded-lg object-cover border border-hairline">
      <div class="flex flex-col gap-2 w-full">
        <label for="name" class="text-body-mid font-semibold text-ink">Nama Klub :</label>
        <input type="text" id="name" name="name" value="{{ $clubRequest->name }}"
          class="border border-hairline rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-primary" readonly>
      </div>
      <div class="flex flex-col gap-2 w-full">
        <label for="description" class="text-body-mid font-semibold text-ink">Deskripsi :</label>
        <textarea id="description" name="description"
          class="border border-hairline rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-primary" readonly>{{ $clubRequest->description }}</textarea>
      </div>
      <div class="flex flex-col gap-2 w-full">
        <label for="category" class="text-body-mid font-semibold text-ink">Kategori Klub :</label>
        <input type="text" id="category" name="category" value="{{ $clubRequest->hobby->name }}"
          class="border border-hairline rounded-lg p-2 w-1/3 focus:outline-none focus:ring-2 focus:ring-primary" readonly>
      </div>
      <div class="flex flex-col gap-2 w-full">
        <label for="reason" class="text-body-mid font-semibold text-ink">Alasan Pengajuan :</label>
        <textarea id="reason" name="reason"
          class="border border-hairline rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-primary" readonly>{{ $clubRequest->reason }}</textarea>
      </div>
      @if ($clubRequest->status === 'rejected')
        <div class="flex flex-col gap-2 w-full">
          <label for="rejection_reason" class="text-body-mid font-semibold text-ink">Alasan Ditolak :</label>
          <textarea id="rejection_reason" name="rejection_reason"
            class="border border-hairline rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-primary" readonly>{{ $clubRequest->rejected_reason }}</textarea>
        </div>
      @endif
      @if ($clubRequest->status === 'pending')
        <div class="flex flex-row w-full gap-4">
          <button
            class="flex flex-row w-1/2 items-center cursor-pointer gap-2 px-6 py-2 lg:w-max bg-primary/10 text-primary hover:text-white hover:bg-primary rounded-lg"
            type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-check">
              <path d="M20 6 9 17l-5-5" />
            </svg>
            Terima
          </button>
          <button type="button" @click="rejectOpen = true"
          class="flex flex-row w-full items-center cursor-pointer gap-2 px-6 py-2 lg:w-max bg-accent-red/10 text-accent-red hover:text-white hover:bg-accent-red rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-x">
            <path d="M18 6 6 18" />
            <path d="m6 6 12 12" />
          </svg>
          Tolak</button>
        </div>
      @endif
    </form>
    <div x-show="rejectOpen"x-cloak @keydown.escape.window="rejectOpen = false" class="w-screen h-dvh flex justify-center items-center fixed inset-0 z-50">
      <div
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="rejectOpen = false"
        class="fixed inset-0 z-40 bg-black/30"></div>

      <div
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        @click.stop
        class="fixed h-max rounded-lg z-50 w-2xs lg:w-lg bg-canvas border border-hairline overflow-hidden p-4">
        <div class="flex flex-col gap-1 mb-4">
          <h2 class="text-heading-2 text-ink">Apakah anda yakin?</h2>
          <p class="text-caption text-ink-muted">Apakah anda yakin ingin menolak permintaan klub ini? Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <form action="{{ route('admin.clubs.request.reject', $clubRequest->id) }}" method="POST">
          @csrf
          @method('patch')
          <div class="flex flex-col gap-2">
            <label for="reason" class="text-body-mid font-semibold text-ink">Alasan Ditolak :</label>
            <textarea id="reason" name="reason" placeholder="Deskripsikan alasan pengajuan anda..."
              class="rounded-lg px-4 py-2 border border-hairline focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
              required></textarea>
          </div>
          <div class="flex w-full flex-row items-center justify-center lg:justify-between gap-4 mt-4">
            <button type="submit"
              class="flex flex-row w-1/2 items-center cursor-pointer gap-2 px-6 py-2 lg:w-max bg-accent-red/10 text-accent-red hover:text-white hover:bg-accent-red rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-x">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
              </svg>
              Tolak</button>
            <button type="button" @click="rejectOpen = false"
              class="flex flex-row w-1/2 items-center cursor-pointer gap-2 px-6 py-2 lg:w-max bg-gray-100 text-gray-600 hover:text-white hover:bg-gray-600 rounded-lg">
              Batal</button>
          </div>
        </form>
      </div>
    </div>
  </main>
@endsection
