@extends('layouts.app')

@section('title', 'Orbii | Club Request Details')

@section('content')
  @php
    $status = $clubRequest->status;
    $requesterName = $clubRequest->requester?->name ?? 'Unknown user';
    $requesterInitial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($requesterName, 0, 1));
  @endphp

  <header class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="flex min-w-0 items-start gap-3">
      <a href="{{ route('admin.clubs.request') }}" aria-label="Back to club requests"
        class="mt-0.5 flex size-9 shrink-0 items-center justify-center bg-canvas text-ink-muted transition-colors duration-150 hover:border-primary/40 hover:text-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m12 19-7-7 7-7" />
          <path d="M19 12H5" />
        </svg>
      </a>

      <div class="min-w-0">
        <div class="flex flex-wrap items-center gap-2">
          <h1 class="text-2xl font-semibold text-ink">Club Request</h1>
          @if ($status === 'pending')
            <span
              class="inline-flex items-center gap-1.5 rounded-full bg-accent-yellow/15 px-2.5 py-1 text-overline font-semibold uppercase tracking-wide text-ink">
              <span class="size-1.5 rounded-full bg-accent-yellow"></span>
              Pending
            </span>
          @elseif ($status === 'approved')
            <span
              class="inline-flex items-center gap-1.5 rounded-full bg-accent-green/10 px-2.5 py-1 text-overline font-semibold uppercase tracking-wide text-accent-green">
              <span class="size-1.5 rounded-full bg-accent-green"></span>
              Approved
            </span>
          @else
            <span
              class="inline-flex items-center gap-1.5 rounded-full bg-accent-red/10 px-2.5 py-1 text-overline font-semibold uppercase tracking-wide text-accent-red">
              <span class="size-1.5 rounded-full bg-accent-red"></span>
              Rejected
            </span>
          @endif
        </div>
        <p class="mt-1 text-caption text-ink-muted">
          Submitted by {{ $requesterName }}
          @if ($clubRequest->created_at)
            on {{ $clubRequest->created_at->format('d M Y, H:i') }}
          @endif
        </p>
      </div>
    </div>
  </header>

  <main x-data="{ showAccept: false, showReject: {{ $errors->reject->any() ? 'true' : 'false' }}, submitting: false }"
    class="grid grid-cols-1 items-start gap-4 lg:grid-cols-3">
    <section class="flex flex-col gap-4 lg:col-span-2">
      {{-- Club overview --}}
      <div class="overflow-hidden rounded-lg border border-hairline bg-canvas">
        <div class="relative h-48 bg-canvas-light sm:h-64">
          @if ($clubRequest->cover_url)
            <img src="{{ Storage::url($clubRequest->cover_url) }}" alt="{{ $clubRequest->name }} cover" loading="lazy"
              class="h-full w-full object-cover">
          @else
            <div class="flex h-full w-full items-center justify-center bg-canvas-light">
              <span
                class="text-heading-1 font-bold text-ink-faint">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($clubRequest->name ?? '?', 0, 1)) }}</span>
            </div>
          @endif

          <span
            class="absolute left-4 top-4 inline-flex items-center rounded-full bg-canvas/95 px-2.5 py-1 text-overline font-semibold uppercase tracking-wide text-primary shadow-sm backdrop-blur">
            {{ $clubRequest->hobby?->name ?? 'Uncategorized' }}
          </span>
        </div>

        <div class="p-5">
          <h2 class="text-title text-ink">{{ $clubRequest->name }}</h2>
          <p class="mt-2 whitespace-pre-line text-caption text-ink-muted">
            {{ $clubRequest->description ?: 'No description provided.' }}
          </p>

          <dl class="mt-6 grid grid-cols-1 gap-x-6 gap-y-4 border-t border-hairline pt-5 sm:grid-cols-2">
            <div>
              <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Category</dt>
              <dd class="mt-1 flex items-center gap-2 text-caption text-ink">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="text-ink-muted">
                  <path d="m15 5 6.3 6.3a2.4 2.4 0 0 1 0 3.4L17 19" />
                  <path
                    d="M9.586 5.586A2 2 0 0 0 8.172 5H3a1 1 0 0 0-1 1v5.172a2 2 0 0 0 .586 1.414L8.29 18.29a2.426 2.426 0 0 0 3.42 0l3.58-3.58a2.426 2.426 0 0 0 0-3.42z" />
                  <circle cx="6.5" cy="9.5" r=".5" fill="currentColor" />
                </svg>
                {{ $clubRequest->hobby?->name ?? 'Uncategorized' }}
              </dd>
            </div>

            <div>
              <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Visibility</dt>
              <dd class="mt-1 flex items-center gap-2 text-caption text-ink">
                @if ($clubRequest->visibility_club === 'private')
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="text-ink-muted">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                  </svg>
                @else
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="text-ink-muted">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                    <path d="M2 12h20" />
                  </svg>
                @endif
                {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $clubRequest->visibility_club ?? 'public')) }}
              </dd>
            </div>

            <div>
              <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Submitted</dt>
              <dd class="mt-1 text-caption text-ink">
                {{ $clubRequest->created_at?->format('d M Y, H:i') ?? 'Not available' }}
              </dd>
            </div>

            <div>
              <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Last reviewed</dt>
              <dd class="mt-1 text-caption text-ink">
                @if ($clubRequest->reviewed_at)
                  {{ $clubRequest->reviewed_at->format('d M Y, H:i') }}
                @else
                  <span class="text-ink-muted">Not reviewed yet</span>
                @endif
              </dd>
            </div>
          </dl>
        </div>
      </div>

      {{-- Reason for request --}}
      <div class="rounded-lg border border-hairline bg-canvas p-5">
        <div class="flex items-center gap-2">
          <span class="flex size-8 items-center justify-center rounded-md bg-primary/10 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
              <polyline points="14 2 14 8 20 8" />
              <line x1="8" x2="16" y1="13" y2="13" />
              <line x1="8" x2="16" y1="17" y2="17" />
            </svg>
          </span>
          <h3 class="text-body-mid font-semibold text-ink">Reason for request</h3>
        </div>
        <p class="mt-3 whitespace-pre-line text-caption text-ink-muted">
          {{ $clubRequest->reason ?: 'No reason provided.' }}
        </p>
      </div>

      {{-- Rejection reason --}}
      @if ($status === 'rejected' && $clubRequest->rejected_reason)
        <div class="rounded-lg border border-accent-red/30 bg-accent-red/5 p-5">
          <div class="flex items-center gap-2">
            <span class="flex size-8 items-center justify-center rounded-md bg-accent-red/10 text-accent-red">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" x2="12" y1="8" y2="12" />
                <line x1="12" x2="12.01" y1="16" y2="16" />
              </svg>
            </span>
            <h3 class="text-body-mid font-semibold text-accent-red">Rejection reason</h3>
          </div>
          <p class="mt-3 whitespace-pre-line text-caption text-accent-red">
            {{ $clubRequest->rejected_reason }}
          </p>
        </div>
      @endif
    </section>

    {{-- Summary and actions --}}
    <aside class="flex flex-col gap-4">
      <div class="rounded-lg border border-hairline bg-canvas p-5">
        <h2 class="text-body-mid font-semibold text-ink">Request summary</h2>

        <div class="mt-4">
          @if ($status === 'pending')
            <span
              class="inline-flex items-center gap-1.5 rounded-full bg-accent-yellow/15 px-2.5 py-1 text-overline font-semibold uppercase tracking-wide text-ink">
              <span class="size-1.5 rounded-full bg-accent-yellow"></span>
              Awaiting review
            </span>
          @elseif ($status === 'approved')
            <span
              class="inline-flex items-center gap-1.5 rounded-full bg-accent-green/10 px-2.5 py-1 text-overline font-semibold uppercase tracking-wide text-accent-green">
              <span class="size-1.5 rounded-full bg-accent-green"></span>
              Approved
            </span>
          @else
            <span
              class="inline-flex items-center gap-1.5 rounded-full bg-accent-red/10 px-2.5 py-1 text-overline font-semibold uppercase tracking-wide text-accent-red">
              <span class="size-1.5 rounded-full bg-accent-red"></span>
              Rejected
            </span>
          @endif
        </div>

        <dl class="mt-5 space-y-4 border-t border-hairline pt-5">
          <div>
            <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Requester</dt>
            <dd class="mt-1.5 flex min-w-0 items-center gap-2">
              <span
                class="flex size-7 shrink-0 items-center justify-center rounded-full bg-primary/10 text-small font-semibold text-primary">{{ $requesterInitial }}</span>
              <span class="truncate text-caption font-medium text-ink">{{ $requesterName }}</span>
            </dd>
          </div>

          <div>
            <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Category</dt>
            <dd class="mt-1.5 text-caption text-ink">{{ $clubRequest->hobby?->name ?? 'Uncategorized' }}</dd>
          </div>

          <div>
            <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Visibility</dt>
            <dd class="mt-1.5 text-caption text-ink">
              {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $clubRequest->visibility_club ?? 'public')) }}
            </dd>
          </div>

          <div>
            <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Submitted</dt>
            <dd class="mt-1.5 text-caption text-ink">{{ $clubRequest->created_at?->format('d M Y, H:i') ?? '—' }}</dd>
          </div>

          @if ($clubRequest->reviewed_at)
            <div>
              <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Reviewed</dt>
              <dd class="mt-1.5 text-caption text-ink">
                {{ $clubRequest->reviewed_at->format('d M Y, H:i') }}
                @if ($clubRequest->reviewer)
                  <span class="block text-small text-ink-muted">by {{ $clubRequest->reviewer->name }}</span>
                @endif
              </dd>
            </div>
          @endif
        </dl>

        @if ($status === 'pending')
          <div class="mt-6 flex flex-col gap-2 border-t border-hairline pt-5">
            <button type="button" @click="showAccept = true"
              class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-2.5 text-caption font-semibold text-white transition-colors duration-150 hover:bg-primary/90">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6 9 17l-5-5" />
              </svg>
              Approve request
            </button>
            <button type="button" @click="showReject = true"
              class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-accent-red/30 bg-accent-red/5 px-4 py-2.5 text-caption font-semibold text-accent-red transition-colors duration-150 hover:bg-accent-red hover:text-white">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
              </svg>
              Reject request
            </button>
          </div>
        @else
          <div class="mt-5 border-t border-hairline pt-5">
            <p class="text-caption text-ink-muted">
              This request has been
              <span class="font-semibold text-ink">{{ $status }}</span>.
              No further action is required.
            </p>
          </div>
        @endif
      </div>

      <div class="rounded-lg border border-hairline bg-canvas-soft p-4">
        <p class="text-small leading-relaxed text-ink-muted">
          Approving a request creates the club and assigns the requester as its owner. Rejecting requires a reason,
          which is shared with the requester.
        </p>
      </div>
    </aside>

    {{-- Approve confirmation modal --}}
    <div x-show="showAccept" x-cloak x-transition.opacity @keydown.escape.window="showAccept = false"
      class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/40" @click="showAccept = false"></div>

      <div role="dialog" aria-modal="true" aria-labelledby="accept-detail-title"
        class="relative z-10 w-full max-w-md rounded-lg border border-hairline bg-canvas p-6 shadow-xl">
        <div class="flex items-start gap-3">
          <div>
            <h2 id="accept-detail-title" class="text-title font-semibold text-ink">Approve this club request?</h2>
            <p class="mt-2 text-caption text-ink-muted">
              A new club <span class="font-semibold text-ink">{{ $clubRequest->name }}</span> will be created and
              <span class="font-semibold text-ink">{{ $requesterName }}</span> will become its owner.
            </p>
          </div>
        </div>

        <form action="{{ route('admin.clubs.request.accept', $clubRequest->id) }}" method="POST"
          @submit="submitting = true" class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
          @csrf
          @method('PATCH')

          <button type="button" @click="showAccept = false" :disabled="submitting"
            class="rounded-md border border-hairline bg-canvas px-4 py-2 text-caption font-semibold text-ink transition-colors duration-150 hover:bg-canvas-soft disabled:cursor-not-allowed disabled:opacity-50">
            Cancel
          </button>
          <button type="submit" :disabled="submitting"
            class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-caption font-semibold text-white transition-colors duration-150 hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-60">
            <svg x-show="!submitting" xmlns="http://www.w3.org/2000/svg" width="15" height="15"
              viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <path d="M20 6 9 17l-5-5" />
            </svg>
            <svg x-show="submitting" x-cloak class="size-4 animate-spin" viewBox="0 0 24 24" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z" />
            </svg>
            <span x-text="submitting ? 'Approving...' : 'Approve request'"></span>
          </button>
        </form>
      </div>
    </div>

    {{-- Reject confirmation modal --}}
    <div x-show="showReject" x-cloak x-transition.opacity @keydown.escape.window="showReject = false"
      class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/40" @click="showReject = false"></div>

      <div role="dialog" aria-modal="true" aria-labelledby="reject-detail-title"
        class="relative z-10 max-h-[calc(100dvh-2rem)] w-full max-w-md overflow-y-auto rounded-lg border border-hairline bg-canvas p-6 shadow-xl">
        <div class="flex items-start gap-3">
          <div>
            <h2 id="reject-detail-title" class="text-title font-semibold text-ink">Reject this club request?</h2>
            <p class="mt-2 text-caption text-ink-muted">
              The request for <span class="font-semibold text-ink">{{ $clubRequest->name }}</span> will be rejected.
              This action cannot be undone.
            </p>
          </div>
        </div>

        <form action="{{ route('admin.clubs.request.reject', $clubRequest->id) }}" method="POST"
          @submit="submitting = true" class="mt-5">
          @csrf
          @method('PATCH')

          <label for="reject-reason" class="block text-caption font-semibold text-ink">Reason for rejection</label>
          <textarea id="reject-reason" name="reason" rows="4" required minlength="5" maxlength="255"
            placeholder="Explain why this request cannot be approved..."
            class="mt-2 w-full rounded-lg border border-hairline bg-canvas px-3 py-2 text-caption text-ink placeholder:text-ink-faint focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">{{ old('reason') }}</textarea>

          <div class="mt-1.5 flex items-center justify-between gap-3 text-small text-ink-faint">
            <span>Minimum 5 characters.</span>
            <span>Maximum 255 characters.</span>
          </div>

          @if ($errors->reject->has('reason'))
            <p class="mt-2 text-caption text-accent-red">{{ $errors->reject->first('reason') }}</p>
          @endif

          <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <button type="button" @click="showReject = false" :disabled="submitting"
              class="rounded-md border border-hairline bg-canvas px-4 py-2 text-caption font-semibold text-ink transition-colors duration-150 hover:bg-canvas-soft disabled:cursor-not-allowed disabled:opacity-50">
              Cancel
            </button>
            <button type="submit" :disabled="submitting"
              class="inline-flex items-center justify-center gap-2 rounded-md bg-accent-red px-4 py-2 text-caption font-semibold text-white transition-colors duration-150 hover:bg-accent-red/90 disabled:cursor-not-allowed disabled:opacity-60">
              <svg x-show="!submitting" xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
              </svg>
              <svg x-show="submitting" x-cloak class="size-4 animate-spin" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z" />
              </svg>
              <span x-text="submitting ? 'Rejecting...' : 'Reject request'"></span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
@endsection
