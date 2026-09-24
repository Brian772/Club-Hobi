@extends('layouts.app')

@section('title', 'Orbii | Club Request Details')

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
    <h2 class="text-title lg:text-2xl font-semibold flex flex-row items-center gap-2 justify-center text-ink">Club Request
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

  <main x-data="{ showReject: false, showAccept: false }" class="mt-6 max-w-4xl">


    <div class="flex flex-col gap-4 rounded-3xl border border-hairline p-4">
      <img src="{{ Storage::url($clubRequest->cover_url) }}" alt="{{ $clubRequest->name }} Cover"
        class="w-full lg:w-lg h-40 lg:h-64 mb-4 rounded-lg object-cover border border-hairline">
      <div class="flex flex-col gap-1">
        <h3 class="text-ink text-title">{{ $clubRequest->name }}</h3>
        <p class="text-ink text-caption font-semibold">Kategori : <span
            class="text-ink-muted">{{ $clubRequest->hobby->name }}</span></p>
      </div>

      <div class="flex flex-col gap-1">
        <h4 class="text-ink text-body-mid">Deskripsi :</h4>
        <p class="text-ink-muted text-caption">{{ $clubRequest->description }}</p>
      </div>

      <div class="flex flex-col gap-1">
        <h4 class="text-ink text-body-mid">Alasan Pengajuan :</h4>
        <p class="text-ink-muted text-caption">{{ $clubRequest->reason }}</p>
      </div>

      @if ($clubRequest->status === 'rejected' && $clubRequest->rejected_reason)
        <div class="flex flex-col gap-1">
          <h4 class="text-ink text-body-mid">Alasan Ditolak :</h4>
          <p class="text-ink-muted text-caption">{{ $clubRequest->rejected_reason }}</p>
        </div>
      @endif
    </div>
    @if ($clubRequest->status === 'pending')
      <div class="flex flex-row w-full gap-2 mt-4">
        <form action="{{ route('admin.clubs.request.accept', $clubRequest->id) }}" method="POST" x-ref="acceptForm"
          @submit.prevent="showAccept = true" class="w-1/2 lg:w-max">
          @csrf
          @method('PATCH')
          <button
            class="flex flex-row w-full items-center justify-center cursor-pointer gap-2 px-6 py-2 lg:w-max bg-primary/10 text-primary hover:text-white hover:bg-primary rounded-md"
            type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-check">
              <path d="M20 6 9 17l-5-5" />
            </svg>
            Terima
          </button>
        </form>
        <button type="button" @click="showReject = true"
          class="flex flex-row w-1/2 items-center justify-center cursor-pointer gap-2 px-6 py-2 lg:w-max bg-accent-red/10 text-accent-red hover:text-white hover:bg-accent-red rounded-md">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
            <path d="M18 6 6 18" />
            <path d="m6 6 12 12" />
          </svg>
          Tolak
        </button>
      </div>
    @endif

    {{-- Accept Modal --}}
    <div x-show="showAccept" x-cloak @keydown.escape.window="showAccept = false"
      class="fixed flex items-center justify-center inset-0 z-50">
      <div @click="showAccept = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
      </div>
      <div class="fixed p-6 h-max rounded-lg z-50 w-max bg-canvas border border-hairline overflow-hidden max-w-md">
        <div class="flex flex-col gap-2 mb-4">
          <h3 class="text-title mb-4 text-ink">
            Terima Ajuan Klub
          </h3>
          <p class="text-body-mid mb-4 text-ink">Apakah anda yakin ingin menerima pengajuan klub ini?</p>
        </div>
        <div class="flex flex-row justify-end gap-2">
          <button type="button" @click="showAccept = false; $refs.cancelForm.submit();"
            class="flex flex-row gap-2 items-center px-4 py-2 text-caption bg-primary/10 rounded w-max text-primary hover:text-white hover:bg-primary">
            Ya, Terima
          </button>
          <button type="button"
            class="bg-gray-200 border border-hairline rounded text-caption px-4 py-2 text-ink hover:bg-gray-300"
            @click="showAccept = false">Cancel</button>
        </div>
      </div>
    </div>

    {{-- Reject Modal --}}
    <div x-show="showReject"x-cloak @keydown.escape.window="showReject = false"
      class="w-screen h-dvh flex justify-center items-center fixed inset-0 z-50">
      <div x-transition.opacity @click="showReject = false" class="fixed inset-0 z-40 bg-black/30"></div>

      <div x-transition.opacity @click.stop
        class="fixed h-max rounded-lg z-50 w-2xs lg:w-lg bg-canvas border border-hairline overflow-hidden p-4">
        <div class="flex flex-col gap-1 mb-4">
          <h2 class="text-title text-ink mb-4">Apakah anda yakin?</h2>
          <p class="text-body-mid text-ink-muted">Apakah anda yakin ingin menolak permintaan klub ini? Tindakan ini
            tidak
            dapat dibatalkan.</p>
        </div>
        <form action="{{ route('admin.clubs.request.reject', $clubRequest->id) }}" method="POST">
          @csrf
          @method('patch')
          <div class="flex flex-col gap-2 mb-4">
            <label for="reason" class="text-body-mid font-semibold text-ink">Alasan Ditolak :</label>
            <textarea id="reason" name="reason" placeholder="Deskripsikan alasan pengajuan anda..."
              class="rounded-lg px-4 py-2 border border-hairline focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
              required></textarea>
          </div>
          <div class="flex w-full flex-row items-center justify-end gap-2 mt-4">
            <button type="submit"
              class="flex flex-row w-1/2 items-center cursor-pointer gap-2 px-6 py-2 lg:w-max bg-accent-red/10 text-accent-red hover:text-white hover:bg-accent-red rounded">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-x">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
              </svg>
              Tolak</button>
            <button type="button" @click="showReject = false"
              class="flex flex-row w-1/2 items-center justify-center cursor-pointer gap-2 px-6 py-2 lg:w-max bg-gray-200 text-ink hover:bg-gray-300 rounded">
              Batal</button>
          </div>
        </form>
      </div>
    </div>
  </main>
@endsection
