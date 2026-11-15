@extends('layouts.app')

@section('title', 'Orbii | Club Details')

@section('content')
  @php
    $ownerName = $clubs->creator?->name ?? 'Unknown user';
    $ownerInitial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($ownerName, 0, 1));
    $memberCount = $clubs->members->count();
    $moderatorCount = $clubs->members->where('role', 'moderator')->count();
  @endphp

  <header class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="flex min-w-0 items-start gap-3">
      <a href="{{ route('admin.club-management') }}" aria-label="Back to club management"
        class="mt-0.5 flex size-9 shrink-0 items-center justify-center bg-canvas text-ink-muted transition-colors duration-150 hover:border-primary/40 hover:text-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m12 19-7-7 7-7" />
          <path d="M19 12H5" />
        </svg>
      </a>

      <div class="min-w-0">
        <div class="flex flex-wrap items-center gap-2">
          <h1 class="text-2xl font-semibold text-ink">{{ $clubs->name }}</h1>
          @if ($clubs->visibility === 'private')
            <span
              class="inline-flex items-center gap-1.5 rounded-full bg-accent-yellow/15 px-2.5 py-1 text-overline font-semibold uppercase tracking-wide text-ink">
              <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
              Private
            </span>
          @else
            <span
              class="inline-flex items-center gap-1.5 rounded-full bg-accent-green/10 px-2.5 py-1 text-overline font-semibold uppercase tracking-wide text-accent-green">
              <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                <path d="M2 12h20" />
              </svg>
              Public
            </span>
          @endif
        </div>
        <p class="mt-1 text-caption text-ink-muted">
          Created by {{ $ownerName }}
          @if ($clubs->created_at)
            on {{ $clubs->created_at->format('d M Y, H:i') }}
          @endif
        </p>
      </div>
    </div>
  </header>

  <main x-data="{
    openDelete: @js($errors->deleteClub->isNotEmpty()),
    selectedName: @js($errors->deleteClub->isNotEmpty() ? $clubs->name : ''),
    confirmText: @js(old('confirm_name', '')),
    deleteUrl: @js($errors->deleteClub->isNotEmpty() ? route('admin.club-management.delete', $clubs->id) : ''),
  }" class="grid grid-cols-1 items-start gap-4 lg:grid-cols-3">
    <section class="flex flex-col gap-4 lg:col-span-2">
      {{-- Club overview --}}
      <div class="overflow-hidden rounded-lg border border-hairline bg-canvas">
        <div class="relative h-48 bg-canvas-light sm:h-64">
          @if ($clubs->cover_url)
            <img src="{{ Storage::url($clubs->cover_url) }}" alt="{{ $clubs->name }} cover" loading="lazy"
              class="h-full w-full object-cover">
          @else
            <div class="flex h-full w-full items-center justify-center bg-canvas-light">
              <span
                class="text-heading-1 font-bold text-ink-faint">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($clubs->name ?? '?', 0, 1)) }}</span>
            </div>
          @endif

          <span
            class="absolute left-4 top-4 inline-flex items-center rounded-full bg-canvas/95 px-2.5 py-1 text-overline font-semibold uppercase tracking-wide text-primary shadow-sm backdrop-blur">
            {{ $clubs->hobby?->name ?? 'Uncategorized' }}
          </span>
        </div>

        <div class="p-5">
          <h2 class="text-title text-ink">{{ $clubs->name }}</h2>
          <p class="mt-2 whitespace-pre-line text-caption text-ink-muted">
            {{ $clubs->description ?: 'No description provided.' }}
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
                {{ $clubs->hobby?->name ?? 'Uncategorized' }}
              </dd>
            </div>

            <div>
              <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Visibility</dt>
              <dd class="mt-1 flex items-center gap-2 text-caption text-ink">
                @if ($clubs->visibility === 'private')
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
                {{ \Illuminate\Support\Str::title($clubs->visibility ?? 'public') }}
              </dd>
            </div>

            <div>
              <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Created</dt>
              <dd class="mt-1 text-caption text-ink">
                {{ $clubs->created_at?->format('d M Y, H:i') ?? 'Not available' }}
              </dd>
            </div>

            <div>
              <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Join approval</dt>
              <dd class="mt-1 text-caption text-ink">
                {{ $clubs->is_required_request ? 'Required' : 'Open to all' }}
              </dd>
            </div>
          </dl>
        </div>
      </div>

      {{-- Club statistics --}}
      <div class="rounded-lg border border-hairline bg-canvas p-5">
        <div class="flex items-center gap-2">
          <span class="flex size-8 items-center justify-center rounded-md bg-primary/10 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
          </span>
          <h3 class="text-body-mid font-semibold text-ink">Club statistics</h3>
        </div>

        <dl class="mt-4 grid grid-cols-2 gap-4">
          <div class="rounded-md border border-hairline bg-canvas-light p-3">
            <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Members</dt>
            <dd class="mt-1 text-xl font-bold text-ink">{{ $memberCount }}</dd>
          </div>
          <div class="rounded-md border border-hairline bg-canvas-light p-3">
            <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Moderators</dt>
            <dd class="mt-1 text-xl font-bold text-ink">{{ $moderatorCount }}</dd>
          </div>
        </dl>
      </div>
    </section>

    {{-- Sidebar summary --}}
    <aside class="flex flex-col gap-4">
      <div class="rounded-lg border border-hairline bg-canvas p-5">
        <h2 class="text-body-mid font-semibold text-ink">Club summary</h2>

        <dl class="mt-5 space-y-4 border-t border-hairline pt-5">
          <div>
            <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Owner</dt>
            <dd class="mt-1.5 flex min-w-0 items-center gap-2">
              <span
                class="flex size-7 shrink-0 items-center justify-center rounded-full bg-primary/10 text-small font-semibold text-primary">{{ $ownerInitial }}</span>
              <span class="truncate text-caption font-medium text-ink">{{ $ownerName }}</span>
            </dd>
          </div>

          <div>
            <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Category</dt>
            <dd class="mt-1.5 text-caption text-ink">{{ $clubs->hobby?->name ?? 'Uncategorized' }}</dd>
          </div>

          <div>
            <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Visibility</dt>
            <dd class="mt-1.5 text-caption text-ink">
              {{ \Illuminate\Support\Str::title($clubs->visibility ?? 'public') }}
            </dd>
          </div>

          <div>
            <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Members</dt>
            <dd class="mt-1.5 text-caption text-ink">{{ $memberCount }} members</dd>
          </div>

          <div>
            <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Created</dt>
            <dd class="mt-1.5 text-caption text-ink">{{ $clubs->created_at?->format('d M Y, H:i') ?? '—' }}</dd>
          </div>

          @if ($clubs->updated_at)
            <div>
              <dt class="text-overline font-semibold uppercase tracking-wide text-ink-faint">Last updated</dt>
              <dd class="mt-1.5 text-caption text-ink">{{ $clubs->updated_at->format('d M Y, H:i') }}</dd>
            </div>
          @endif
        </dl>
      </div>

      {{-- Danger zone --}}
      <div class="rounded-lg border border-accent-red/30 bg-accent-red/5 p-5">
        <div class="flex items-center gap-2">
          <span class="flex size-8 items-center justify-center rounded-md bg-accent-red/10 text-accent-red">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
              <line x1="12" x2="12" y1="9" y2="13" />
              <line x1="12" x2="12.01" y1="17" y2="17" />
            </svg>
          </span>
          <h3 class="text-body-mid font-semibold text-accent-red">Danger zone</h3>
        </div>
        <p class="mt-3 text-caption text-accent-red/80">
          Deleting this club is permanent. All data and content will be lost and cannot be recovered.
        </p>
        <button type="button"
          @click="openDelete = true;
            confirmText = ''; selectedName = '{{ $clubs->name }}'; deleteUrl = '{{ route('clubs.delete', $clubs->id) }}';"
          class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-md border border-accent-red/30 bg-accent-red/5 px-4 py-2.5 text-caption font-semibold text-accent-red transition-colors duration-150 hover:bg-accent-red hover:text-white">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10 11v6" />
            <path d="M14 11v6" />
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
            <path d="M3 6h18" />
            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
          </svg>
          Delete this club
        </button>
      </div>

      <div class="rounded-lg border border-hairline bg-canvas-soft p-4">
        <p class="text-small leading-relaxed text-ink-muted">
          Club ID: <span class="font-mono text-ink">{{ $clubs->id }}</span>
        </p>
      </div>
    </aside>

    {{-- Delete confirmation modal --}}
    <div x-show="openDelete" x-cloak x-transition.opacity @keydown.escape.window="openDelete = false"
      class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/40" @click="openDelete = false"></div>

      <div role="dialog" aria-modal="true" aria-labelledby="delete-detail-title"
        class="relative z-10 max-h-[calc(100dvh-2rem)] w-full max-w-md overflow-y-auto rounded-lg border border-hairline bg-canvas p-6 shadow-xl">
        <div class="flex items-start gap-3">
          <div>
            <h2 id="delete-detail-title" class="text-title font-semibold text-ink">Delete <span
                x-text="selectedName"></span>?</h2>
            <p class="mt-2 text-caption text-ink-muted">
              Are you sure you want to delete this club? All data and content will be permanently deleted and this
              action cannot be undone.
            </p>
          </div>
        </div>

        <form :action="deleteUrl" method="POST" x-data="{ touched: false, showServerError: true }" class="mt-5">
          @csrf
          @method('DELETE')

          <label for="confirm-name" class="block text-caption font-semibold text-ink-muted">
            Type <span class="font-semibold text-ink" x-text="selectedName"></span> to confirm:
          </label>
          <input type="text" id="confirm-name" name="confirm_name" x-model="confirmText"
            @input="showServerError = false" @blur="touched = true" autocomplete="off"
            :class="(touched && confirmText !== '' && confirmText !== selectedName) ?
                'border-accent-red' : 'border-hairline'"
            class="mt-2 w-full rounded-lg border px-3 py-2 text-caption text-ink placeholder:text-ink-faint focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
            placeholder="Type club name">

          <p x-show="touched && confirmText !== '' && confirmText !== selectedName" x-cloak
            class="mt-1.5 text-caption text-accent-red">
            Club name does not match.
          </p>

          @error('confirm_name', 'deleteClub')
            <p x-show="showServerError" class="mt-1.5 text-caption text-accent-red">{{ $message }}</p>
          @enderror

          <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
              <button type="submit" :disabled="confirmText !== selectedName"
              class="inline-flex items-center justify-center gap-2 rounded-md bg-accent-red px-4 py-2 text-caption font-semibold text-white transition-colors duration-150 hover:bg-accent-red/90 disabled:cursor-not-allowed disabled:opacity-60">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 11v6" />
                <path d="M14 11v6" />
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                <path d="M3 6h18" />
                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
              </svg>
              Delete club
            </button>
            <button type="button" @click="openDelete = false"
              class="rounded-md border border-hairline bg-canvas px-4 py-2 text-caption font-semibold text-ink transition-colors duration-150 hover:bg-canvas-soft">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
@endsection
