@extends('layouts.app')

@section('title', 'Orbii | Request List')

@section('content')
  <header class="flex flex-row gap-2 lg:gap-4 items-center justify-start mb-6">
    <a href="{{ route('clubs.index') }}" class="text-ink-muted">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
        <path d="M0 0h16v9H0z" fill="none" />
        <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
        <path fill="currentColor"
          d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
      </svg>
    </a>
    <h2 class="text-title lg:text-heading-2 text-ink-secondary">Pengajuan Klub Saya</h2>
  </header>
  @if ($clubRequests->isEmpty())
    <div class="flex flex-col h-full justify-center items-center">
      <h2 class="text-title text-ink">Tidak ada pengajuan</h2>
      <p class="text-body-mid text-ink-muted">Anda belum mengajukan klub apa pun.</p>
      <a href="{{ route('clubs.request') }}"
        class="text-primary bg-primary/10 hover:text-white mt-8 hover:bg-primary rounded-md px-4 py-2">+ Ajukan Klub
        Baru</a>
    </div>
  @else
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-start 2xl:grid-cols-3">
      @foreach ($clubRequests as $request)
        <a href="{{ route('clubs.request.detail', $request->id) }}"
          class="group border border-hairline rounded-lg h-max w-full p-4 hover:shadow-lg transition-shadow duration-300">
          <div class="flex flex-col mb-2 w-full">
            <h3 class="text-ink text-heading-3 mb-4">{{ $request->name }}</h3>
            <p class="text-ink-muted text-body-mid">{{ $request->description }}</p>
            <p class="text-ink-muted text-caption"><span class="font-semibold text-ink">Kategori:</span>
              {{ $request->hobby->name }}</p>
            <p class="text-ink-muted text-caption"><span class="font-semibold text-ink">Diajukan </span>
              {{ $request->created_at->format('d M Y') }}</p>
          </div>
          <div class="flex flex-row items-center justify-between">
            <p class="text-ink-muted text-caption"><span class="font-semibold text-ink">Status :</span>
              @if ($request->status === 'pending')
                <span class="text-accent-yellow font-semibold">{{ ucfirst($request->status) }}</span>
              @elseif ($request->status === 'approved')
                <span class="text-accent-green font-semibold">{{ ucfirst($request->status) }}</span>
              @else
                <span class="text-accent-red font-semibold">{{ ucfirst($request->status) }}</span>
              @endif
            </p>
            <span href="{{ route('clubs.request.detail', $request->id) }}"
              class="font-semibold text-ink-muted p-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-arrow-right preview-icon group-hover:translate-x-1 transition-transform duration-300">
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
              </svg>
            </span>
          </div>
        </a>
      @endforeach
    </div>
  @endif
@endsection
