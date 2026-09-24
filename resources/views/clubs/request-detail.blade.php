@extends('layouts.app')

@section('title', 'Orbii | Request Detail')

@section('content')
  <header class="flex flex-row gap-2 lg:gap-4 items-center justify-start mb-6">
    <a href="{{ route('clubs.request.list') }}" class="text-ink-muted">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
        <path d="M0 0h16v9H0z" fill="none" />
        <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
        <path fill="currentColor"
          d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
      </svg>
    </a>
    <h2 class="text-title lg:text-heading-2 flex flex-row items-center gap-2 justify-center text-ink">Request Detail</h2>
  </header>

  <main class="flex flex-col gap-4 max-w-4xl" x-data="{ showCancel: false }">
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

    <div class="flex flex-col gap-1">
      <h4 class="text-ink text-body-mid">Status :</h4>
      <p class="text-ink-muted text-caption">
        @if ($clubRequest->status === 'pending')
          <span class="text-accent-yellow font-semibold">{{ ucfirst($clubRequest->status) }}</span>
        @elseif ($clubRequest->status === 'approved')
          <span class="text-accent-green font-semibold">{{ ucfirst($clubRequest->status) }}</span>
        @else
          <span class="text-accent-red font-semibold">{{ ucfirst($clubRequest->status) }}</span>
        @endif
      </p>
    </div>

    @if ($clubRequest->status === 'rejected' && $clubRequest->rejected_reason)
      <div class="flex flex-col gap-1">
        <h4 class="text-ink text-body-mid">Alasan Ditolak :</h4>
        <p class="text-ink-muted text-caption">{{ $clubRequest->rejected_reason }}</p>
      </div>
    @endif

    @if ($clubRequest->status !== 'pending')
      <div class="flex flex-col gap-1">
        <h4 class="text-ink text-body-mid">Direview Oleh :</h4>
        <p class="text-ink-muted text-caption">
          {{ $clubRequest->reviewer ? $clubRequest->reviewer->name : 'Belum Direview' }}</p>
      </div>

      <div class="flex flex-col gap-1">
        <h4 class="text-ink text-body-mid">Tanggal Direview :</h4>
        <p class="text-ink-muted text-caption">
          {{ $clubRequest->reviewed_at ? $clubRequest->reviewed_at->format('d M Y') : 'Belum Direview' }}</p>
      </div>
    @endif

    @if ($clubRequest->status === 'pending')
      <form action="{{ route('clubs.request.cancel', $clubRequest->id) }}" method="POST" x-ref="cancelForm"
        @submit.prevent="showCancel = true">
        @csrf
        @method('DELETE')
        <button
          tpye="submit" class="bg-accent-red/10 text-accent-red mt-8 flex flex-row gap-2 items-center hover:text-white rounded-md px-4 py-2 hover:bg-accent-red w-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 6 18M6 6l12 12" />
          </svg>
          Batalkan Pengajuan</button>
      </form>
    @endif

    {{-- Cancel Modal --}}
    <div x-show="showCancel" x-cloak @keydown.escape.window="showCancel = false"
      class="fixed flex items-center justify-center inset-0 z-50">
      <div @click="showCancel = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
      </div>
      <div class="fixed p-6 h-max rounded-lg z-50 w-max bg-canvas border border-hairline overflow-hidden max-w-md">
        <div class="flex flex-col gap-2 mb-4">
          <h3 class="text-title mb-4 text-ink">
            Batal Ajuan Klub
          </h3>
          <p class="text-body-mid mb-4 text-ink">Apakah anda yakin ingin membatalkan pengajuan klub ini?</p>
        </div>
        <div class="flex flex-row justify-end gap-2">
          <button type="button" @click="showCancel = false; $refs.cancelForm.submit();"
            class="flex flex-row gap-2 items-center px-4 py-2 text-caption bg-accent-red/10 rounded w-max text-accent-red hover:text-white hover:bg-accent-red">
            Ya, Batalkan
          </button>
          <button type="button"
            class="bg-gray-200 border border-hairline rounded text-caption px-4 py-2 text-ink hover:bg-gray-300"
            @click="showCancel = false">Cancel</button>
        </div>
      </div>
    </div>
  </main>
@endsection
