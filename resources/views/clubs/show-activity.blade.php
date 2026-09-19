@extends('layouts.app')

@section('content')
  <header class="flex flex-row gap-2 lg:gap-4 items-center justify-start mb-6">
    <a href="{{ route('clubs.settings', $club->id) }}" class="text-ink-muted">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
        <path d="M0 0h16v9H0z" fill="none" />
        <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
        <path fill="currentColor"
          d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
      </svg>
    </a>
    <h2 class="text-title lg:text-2xl font-semibold flex flex-row items-center gap-2 justify-center text-ink">Activity Log
      Details</h2>
    </h2>
  </header>

  <main class="flex flex-col gap-4">
    <section class="w-full p-6 rounded-lg border border-hairline">
      <h2 class="text-ink text-title mb-4">Activity Overview</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex flex-col gap-1">
          <p class="text-ink-muted font-semibold text-body-mid">Performed By:</p>
          <span class="text-caption text-ink">{{ $activity->user?->name ?? 'System' }}</span>
        </div>
        <div class="flex flex-col gap-1">
          <p class="text-ink-muted font-semibold text-body-mid">Action:</p>
          <span class="text-caption text-ink">{{ $activity->action }}</span>
        </div>
        <div class="flex flex-col gap-1">
          <p class="text-ink-muted font-semibold text-body-mid">Target:</p>
          <span class="text-caption text-ink">{{ $activity->target_type ?? 'N/A' }} |
            #{{ $activity->target_id ?? 'N/A' }}</span>
        </div>
        <div class="flex flex-col gap-1">
          <p class="text-ink-muted font-semibold text-body-mid">Created At:</p>
          <span class="text-caption text-ink">{{ $activity->created_at->format('d M Y, H:i') }}</span>
        </div>
      </div>
    </section>

    <section class="w-full p-6 rounded-lg border border-hairline">
      <h2 class="text-ink text-title mb-4">Detail Data</h2>

      <div class="flex flex-col gap-2">
        @forelse ($activity->format_metadata as $label => $value)
          <div class="flex flex-col">
            <span class="text-ink-muted text-body-mid font-semibold">{{ $label }} :</span>
            <span class="text-caption text-ink">{{ is_array($value) ? json_encode($value) : $value }}</span>
          </div>
        @empty
          <span class="text-ink-muted text-body-mid font-semibold">No data available</span>
        @endforelse
      </div>
    </section>
  </main>
@endsection
