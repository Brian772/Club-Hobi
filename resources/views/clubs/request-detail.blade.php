@extends('layouts.app')

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
    <h2 class="text-heading-2 flex flex-row items-center gap-2 justify-center text-ink">Request Detail</h2>
  </header>

  <main class="flex flex-col gap-6 max-w-4xl">
    <img src="{{ Storage::url($clubRequest->cover_url) }}" alt="{{ $clubRequest->name }} Cover"
      class="w-full lg:w-lg h-40 lg:h-64 mb-4 rounded-lg object-cover border border-hairline">
    <div class="flex flex-col">
      <h3 class="text-ink text-heading-3">{{ $clubRequest->name }}</h3>
      <p class="text-ink text-caption font-semibold">Kategori : <span
          class="text-ink-muted">{{ $clubRequest->hobby->name }}</span></p>
    </div>

    <div class="flex flex-col gap-1">
      <h4 class="text-ink text-title">Deskripsi :</h4>
      <p class="text-ink-muted text-body-mid">{{ $clubRequest->description }}</p>
    </div>

    <div class="flex flex-col gap-1">
      <h4 class="text-ink text-title">Alasan Pengajuan :</h4>
      <p class="text-ink-muted text-body-mid">{{ $clubRequest->reason }}</p>
    </div>

    <div class="flex flex-col gap-1">
      <h4 class="text-ink text-title">Status :</h4>
      <p class="text-ink-muted text-body-mid">
        @if ($clubRequest->status === 'pending')
          <span class="text-accent-yellow font-semibold">{{ ucfirst($clubRequest->status) }}</span>
        @elseif ($clubRequest->status === 'approved')
          <span class="text-accent-green font-semibold">{{ ucfirst($clubRequest->status) }}</span>
        @else
          <span class="text-accent-red font-semibold">{{ ucfirst($clubRequest->status) }}</span>
        @endif
      </p>
    </div>

    @if ($clubRequest->status !== 'pending')
      <div class="flex flex-col gap-1">
        <h4 class="text-ink text-title">Direview Oleh :</h4>
        <p class="text-ink-muted text-body-mid">
          {{ $clubRequest->reviewer ? $clubRequest->reviewer->name : 'Belum Direview' }}</p>
      </div>

      <div class="flex flex-col gap-1">
        <h4 class="text-ink text-title">Tanggal Direview :</h4>
        <p class="text-ink-muted text-body-mid">
          {{ $clubRequest->reviewed_at ? $clubRequest->reviewed_at->format('d M Y') : 'Belum Direview' }}</p>
      </div>
    @endif
    @if ($clubRequest->status === 'rejected' && $clubRequest->rejected_reason)
      <div class="flex flex-col gap-1">
        <h4 class="text-ink text-title">Alasan Ditolak :</h4>
        <p class="text-ink-muted text-body-mid">{{ $clubRequest->rejected_reason }}</p>
      </div>
    @endif
  </main>
@endsection
