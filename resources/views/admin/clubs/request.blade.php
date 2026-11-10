@extends('layouts.app')

@section('title', 'Orbii | Club Requests')

@section('content')
  @php
    $totalRequests = $clubRequest->count();
    $pendingRequests = $clubRequest->where('status', 'pending')->count();
    $approvedRequests = $clubRequest->where('status', 'approved')->count();
    $rejectedRequests = $clubRequest->where('status', 'rejected')->count();

    $requestCategories = $clubRequest
        ->map(fn ($request) => $request->hobby)
        ->filter()
        ->unique('id')
        ->sortBy('name')
        ->values();

    $requestsData = $clubRequest
        ->sortByDesc('created_at')
        ->values()
        ->map(function ($request) {
            $requesterName = $request->requester?->name ?? 'Unknown user';

            return [
                'id' => $request->id,
                'name' => $request->name,
                'description' => $request->description ?? '',
                'reason' => $request->reason ?? '',
                'category' => $request->hobby?->name ?? 'Uncategorized',
                'category_id' => (string) $request->hobby_id,
                'requester' => $requesterName,
                'requester_initial' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($requesterName, 0, 1)),
                'status' => $request->status,
                'cover' => $request->cover_url ? Storage::url($request->cover_url) : null,
                'initial' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($request->name ?? '?', 0, 1)),
                'created' => $request->created_at?->format('d M Y'),
                'reviewed' => $request->reviewed_at?->format('d M Y'),
                'visibility' => \Illuminate\Support\Str::title(str_replace('_', ' ', $request->visibility_club ?? 'public')),
                'detail_url' => route('admin.clubs.request.show', $request->id),
                'accept_url' => route('admin.clubs.request.accept', $request->id),
                'reject_url' => route('admin.clubs.request.reject', $request->id),
            ];
        })
        ->all();

    $rejectError = $errors->reject->first('reason');
    $failedRequestId = old('request_id');
  @endphp

  @if ($rejectError && !$failedRequestId)
    <div class="mb-4 flex items-start gap-2 rounded-lg border border-accent-red/30 bg-accent-red/5 p-3" role="alert">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        class="mt-0.5 shrink-0 text-accent-red">
        <circle cx="12" cy="12" r="10" />
        <line x1="12" x2="12" y1="8" y2="12" />
        <line x1="12" x2="12.01" y1="16" y2="16" />
      </svg>
      <p class="text-caption text-accent-red">{{ $rejectError }}</p>
    </div>
  @endif

  <section class="mx-auto" x-data="{
    tab: 'pending',
    search: '',
    searchInput: '',
    searching: false,
    searchTimer: null,
    category: 'all',
    requests: @js($requestsData),
    acceptOpen: false,
    rejectOpen: false,
    submitting: false,
    selected: null,
    acceptAction: '',
    rejectAction: '',
    rejectReason: '',
    rejectError: @js($rejectError),
    init() {
      const failedId = @js($failedRequestId);

      if (failedId) {
        const failedRequest = this.requests.find((request) => request.id === failedId);

        if (failedRequest) {
          this.openReject(failedRequest);
          this.rejectReason = @js(old('reason', ''));
        }
      }
    },
    updateSearch(value) {
      this.searchInput = value;
      this.searching = true;
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(() => {
        this.search = value;
        this.searching = false;
      }, 200);
    },
    clearSearch() {
      clearTimeout(this.searchTimer);
      this.search = '';
      this.searchInput = '';
      this.searching = false;
    },
    resetFilters() {
      this.clearSearch();
      this.category = 'all';
    },
    openAccept(request) {
      this.selected = request;
      this.acceptAction = request.accept_url;
      this.submitting = false;
      this.acceptOpen = true;
    },
    openReject(request) {
      this.selected = request;
      this.rejectAction = request.reject_url;
      this.rejectReason = '';
      this.rejectError = null;
      this.submitting = false;
      this.rejectOpen = true;
    },
    get hasFilters() {
      return this.search !== '' || this.category !== 'all';
    },
    get filteredRequests() {
      const query = this.search.trim().toLowerCase();

      return this.requests.filter((request) => {
        const matchesTab = request.status === this.tab;
        const matchesCategory = this.category === 'all' || request.category_id === this.category;
        const matchesSearch = query === '' || [request.name, request.description, request.reason, request.requester, request.category]
          .some((value) => (value || '').toLowerCase().includes(query));

        return matchesTab && matchesCategory && matchesSearch;
      });
    },
    get emptyTitle() {
      if (this.hasFilters) {
        return 'No requests match your filters';
      }

      return {
        pending: 'No pending requests',
        approved: 'No approved requests',
        rejected: 'No rejected requests'
      }[this.tab] ?? 'No requests found';
    },
    get emptyDescription() {
      if (this.hasFilters) {
        return 'Try a different keyword or category, or clear the filters to see all requests.';
      }

      return {
        pending: 'New club requests will appear here for review.',
        approved: 'Approved club requests will be listed here.',
        rejected: 'Rejected club requests will be listed here.'
      }[this.tab] ?? 'There are no requests to display.';
    }
  }">
    <header class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-ink">Club Requests</h1>
        <p class="mt-1 max-w-2xl text-body-mid text-ink-muted">
          Review requests from members who want to create a new hobby club on Orbii.
        </p>
      </div>

      <span
        class="inline-flex w-max items-center gap-2 rounded-full border border-hairline bg-canvas px-3.5 py-2 text-caption font-medium text-ink-muted">
        <span class="size-1.5 rounded-full bg-accent-yellow"></span>
        {{ $pendingRequests }} awaiting review
      </span>
    </header>

    {{-- Statistics --}}
    <section class="grid grid-cols-2 gap-2 lg:grid-cols-4 lg:gap-4">
      <div class="rounded-lg border border-hairline bg-canvas-soft p-4 transition-shadow duration-150 hover:shadow-sm">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-caption text-ink-muted">Total requests</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ number_format($totalRequests) }}</p>
          </div>
          <span class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-inbox">
              <polyline points="22 12 16 12 14 15 10 15 8 12 2 12" />
              <path
                d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" />
            </svg>
          </span>
        </div>
        <p class="mt-2 text-small text-ink-faint">All time submissions</p>
      </div>

      <div class="rounded-lg border border-hairline bg-canvas-soft p-4 transition-shadow duration-150 hover:shadow-sm">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-caption text-ink-muted">Pending</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ number_format($pendingRequests) }}</p>
          </div>
          <span class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent-yellow/15 text-ink">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-clock">
              <circle cx="12" cy="12" r="10" />
              <polyline points="12 6 12 12 16 14" />
            </svg>
          </span>
        </div>
        <p class="mt-2 text-small text-ink-faint">Awaiting review</p>
      </div>

      <div class="rounded-lg border border-hairline bg-canvas-soft p-4 transition-shadow duration-150 hover:shadow-sm">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-caption text-ink-muted">Approved</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ number_format($approvedRequests) }}</p>
          </div>
          <span class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent-green/10 text-accent-green">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-circle-check">
              <circle cx="12" cy="12" r="10" />
              <path d="m9 12 2 2 4-4" />
            </svg>
          </span>
        </div>
        <p class="mt-2 text-small text-ink-faint">Clubs created</p>
      </div>

      <div class="rounded-lg border border-hairline bg-canvas-soft p-4 transition-shadow duration-150 hover:shadow-sm">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-caption text-ink-muted">Rejected</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ number_format($rejectedRequests) }}</p>
          </div>
          <span class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent-red/10 text-accent-red">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-circle-x">
              <circle cx="12" cy="12" r="10" />
              <path d="m15 9-6 6" />
              <path d="m9 9 6 6" />
            </svg>
          </span>
        </div>
        <p class="mt-2 text-small text-ink-faint">Not approved</p>
      </div>
    </section>

    <main class="mt-4 rounded-lg border border-hairline bg-canvas p-4 lg:p-5">
      {{-- Status tabs --}}
      <div class="border-b border-hairline">
        <nav class="-mb-px flex gap-6 overflow-x-auto" aria-label="Filter requests by status">
          <button type="button" @click="tab = 'pending'"
            :class="tab === 'pending' ? 'border-primary text-primary' : 'border-transparent text-ink-muted hover:text-ink'"
            class="flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-1 text-caption font-semibold transition-colors duration-150">
            Pending
            <span
              :class="tab === 'pending' ? 'bg-accent-yellow/20 text-ink' : 'bg-canvas-soft text-ink-muted'"
              class="rounded-full px-2 py-0.5 text-small font-semibold">{{ $pendingRequests }}</span>
          </button>

          <button type="button" @click="tab = 'approved'"
            :class="tab === 'approved' ? 'border-primary text-primary' : 'border-transparent text-ink-muted hover:text-ink'"
            class="flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-1 text-caption font-semibold transition-colors duration-150">
            Approved
            <span
              :class="tab === 'approved' ? 'bg-accent-green/10 text-accent-green' : 'bg-canvas-soft text-ink-muted'"
              class="rounded-full px-2 py-0.5 text-small font-semibold">{{ $approvedRequests }}</span>
          </button>

          <button type="button" @click="tab = 'rejected'"
            :class="tab === 'rejected' ? 'border-primary text-primary' : 'border-transparent text-ink-muted hover:text-ink'"
            class="flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-1 text-caption font-semibold transition-colors duration-150">
            Rejected
            <span
              :class="tab === 'rejected' ? 'bg-accent-red/10 text-accent-red' : 'bg-canvas-soft text-ink-muted'"
              class="rounded-full px-2 py-0.5 text-small font-semibold">{{ $rejectedRequests }}</span>
          </button>
        </nav>
      </div>

      {{-- Search and filters --}}
      <div class="mt-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-center lg:max-w-3xl">
          <div class="relative w-full sm:max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-muted">
              <path d="m21 21-4.34-4.34" />
              <circle cx="11" cy="11" r="8" />
            </svg>
            <input type="search" :value="searchInput" @input="updateSearch($event.target.value)"
              placeholder="Search club, requester, or category" aria-label="Search club requests"
              class="w-full rounded-lg border border-hairline bg-canvas py-2 pl-9 pr-9 text-caption text-ink placeholder:text-ink-faint focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
            <span x-show="searching" x-cloak class="absolute right-3 top-1/2 -translate-y-1/2 text-primary"
              aria-hidden="true">
              <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z" />
              </svg>
            </span>
            <button type="button" x-show="!searching && searchInput !== ''" x-cloak @click="clearSearch()"
              aria-label="Clear search"
              class="absolute right-2 top-1/2 flex size-6 -translate-y-1/2 items-center justify-center rounded-full text-ink-muted transition-colors hover:bg-canvas-soft hover:text-ink">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
              </svg>
            </button>
          </div>

          <select x-model="category" aria-label="Filter requests by category"
            class="w-full rounded-lg border border-hairline bg-canvas px-3 pr-10 py-2 text-caption text-ink focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 sm:w-auto">
            <option value="all">All categories</option>
            @foreach ($requestCategories as $category)
              <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="flex flex-wrap items-center gap-3 text-caption text-ink-muted">
          <span aria-live="polite"
            x-text="`${filteredRequests.length} ${filteredRequests.length === 1 ? 'request' : 'requests'}`"></span>
          <button type="button" x-show="hasFilters" x-cloak @click="resetFilters()"
            class="font-semibold text-primary hover:underline hover:underline-offset-2">
            Reset filters
          </button>
        </div>
      </div>

      {{-- Request list --}}
      <div class="mt-5">
        <div x-show="filteredRequests.length > 0" x-cloak class="flex flex-col gap-3">
          <template x-for="request in filteredRequests" :key="request.id">
            <article
              class="group flex flex-col overflow-hidden rounded-lg border border-hairline bg-canvas transition-all duration-200 hover:border-primary/20 hover:shadow-md sm:flex-row">
              <a :href="request.detail_url"
                class="relative block h-40 w-full shrink-0 overflow-hidden bg-canvas-light sm:h-auto sm:min-h-40 sm:w-40 lg:w-44"
                :aria-label="`View request for ${request.name}`">
                <template x-if="request.cover">
                  <img :src="request.cover" :alt="`${request.name} cover`" loading="lazy"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
                </template>
                <template x-if="!request.cover">
                  <div class="flex h-full min-h-40 w-full items-center justify-center bg-canvas-light">
                    <span class="text-heading-2 font-bold text-ink-faint" x-text="request.initial"></span>
                  </div>
                </template>
              </a>

              <div class="flex min-w-0 flex-1 flex-col p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                  <div class="min-w-0 flex-1">
                    <a :href="request.detail_url"
                      class="block truncate text-body-mid font-semibold text-ink transition-colors duration-150 hover:text-primary"
                      x-text="request.name"></a>

                    <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-caption text-ink-muted">
                      <span
                        class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-overline font-semibold uppercase tracking-wide text-primary"
                        x-text="request.category"></span>
                      <span class="text-ink-faint" aria-hidden="true">·</span>
                      <span class="inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round">
                          <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                          <line x1="16" x2="16" y1="2" y2="6" />
                          <line x1="8" x2="8" y1="2" y2="6" />
                          <line x1="3" x2="21" y1="10" y2="10" />
                        </svg>
                        <span x-text="`Submitted ${request.created}`"></span>
                      </span>
                      <span class="text-ink-faint" aria-hidden="true">·</span>
                      <span class="inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round">
                          <circle cx="12" cy="12" r="10" />
                          <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                          <path d="M2 12h20" />
                        </svg>
                        <span x-text="request.visibility"></span>
                      </span>
                    </div>
                  </div>

                  <span
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-overline font-semibold uppercase tracking-wide"
                    :class="{
                      'bg-accent-yellow/15 text-ink': request.status === 'pending',
                      'bg-accent-green/10 text-accent-green': request.status === 'approved',
                      'bg-accent-red/10 text-accent-red': request.status === 'rejected'
                    }">
                    <span class="size-1.5 rounded-full" :class="{
                      'bg-accent-yellow': request.status === 'pending',
                      'bg-accent-green': request.status === 'approved',
                      'bg-accent-red': request.status === 'rejected'
                    }"></span>
                    <span x-text="request.status"></span>
                  </span>
                </div>

                <p class="mt-2 line-clamp-2 text-caption text-ink-muted"
                  x-text="request.description || 'No description provided.'"></p>

                <div
                  class="mt-auto flex flex-col gap-3 border-t border-hairline pt-3 sm:flex-row sm:items-center sm:justify-between">
                  <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-caption text-ink-muted">
                    <span class="flex min-w-0 items-center gap-2">
                      <span
                        class="flex size-6 shrink-0 items-center justify-center rounded-full bg-primary/10 text-small font-semibold text-primary"
                        x-text="request.requester_initial"></span>
                      <span class="truncate font-medium text-ink" x-text="request.requester"></span>
                    </span>
                    <span x-show="request.reviewed" class="inline-flex items-center gap-1 text-ink-faint">
                      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m9 12 2 2 4-4" />
                      </svg>
                      <span x-text="`Reviewed ${request.reviewed}`"></span>
                    </span>
                  </div>

                  <div class="flex flex-wrap items-center gap-2">
                    <a :href="request.detail_url"
                      class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-md border border-hairline bg-canvas px-3 py-1.5 text-caption font-semibold text-ink transition-colors duration-150 hover:border-primary/40 hover:text-primary sm:flex-none">
                      View details
                    </a>

                    <div x-show="request.status === 'pending'" class="flex flex-1 items-center gap-2 sm:flex-none">
                      <button type="button" @click="openReject(request)"
                        class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-md border border-accent-red/30 bg-accent-red/5 px-3 py-1.5 text-caption font-semibold text-accent-red transition-colors duration-150 hover:bg-accent-red hover:text-white sm:flex-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round">
                          <path d="M18 6 6 18" />
                          <path d="m6 6 12 12" />
                        </svg>
                        Reject
                      </button>

                      <button type="button" @click="openAccept(request)"
                        class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-md bg-primary px-3 py-1.5 text-caption font-semibold text-white transition-colors duration-150 hover:bg-primary/90 sm:flex-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round">
                          <path d="M20 6 9 17l-5-5" />
                        </svg>
                        Approve
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </article>
          </template>
        </div>

        {{-- Empty state --}}
        <div x-show="filteredRequests.length === 0" x-cloak
          class="flex flex-col items-center justify-center rounded-lg border border-dashed border-hairline bg-canvas-soft px-6 py-14 text-center">
          <span class="flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="22 12 16 12 14 15 10 15 8 12 2 12" />
              <path
                d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" />
            </svg>
          </span>
          <h3 class="mt-4 text-title text-ink" x-text="emptyTitle"></h3>
          <p class="mt-1 max-w-sm text-caption text-ink-muted" x-text="emptyDescription"></p>
          <button type="button" x-show="hasFilters" @click="resetFilters()"
            class="mt-5 inline-flex items-center gap-2 rounded-md bg-primary/10 px-4 py-2 text-caption font-semibold text-primary transition-colors duration-150 hover:bg-primary hover:text-white">
            Clear filters
          </button>
        </div>
      </div>
    </main>

    {{-- Approve confirmation modal --}}
    <div x-show="acceptOpen" x-cloak x-transition.opacity @keydown.escape.window="acceptOpen = false"
      class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/40" @click="acceptOpen = false"></div>

      <div role="dialog" aria-modal="true" aria-labelledby="accept-request-title"
        class="relative z-10 w-full max-w-md rounded-lg border border-hairline bg-canvas p-6 shadow-xl">
        <div class="flex items-start gap-3">          <div class="min-w-0">
            <h2 id="accept-request-title" class="text-title font-semibold text-ink">Approve this club request?</h2>
            <p class="mt-2 text-caption text-ink-muted">
              A new club <span class="font-semibold text-ink" x-text="selected?.name"></span> will be created and
              <span class="font-semibold text-ink" x-text="selected?.requester"></span> will become its owner.
            </p>
          </div>
        </div>

        <form :action="acceptAction" method="POST" @submit="submitting = true"
          class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
          @csrf
          @method('PATCH')

          <button type="button" @click="acceptOpen = false" :disabled="submitting"
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
    <div x-show="rejectOpen" x-cloak x-transition.opacity @keydown.escape.window="rejectOpen = false"
      class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/40" @click="rejectOpen = false"></div>

      <div role="dialog" aria-modal="true" aria-labelledby="reject-request-title"
        class="relative z-10 max-h-[calc(100dvh-2rem)] w-full max-w-md overflow-y-auto rounded-lg border border-hairline bg-canvas p-6 shadow-xl">
        <div class="flex items-start gap-3">
          <div class="min-w-0">
            <h2 id="reject-request-title" class="text-title font-semibold text-ink">Reject this club request?</h2>
            <p class="mt-2 text-caption text-ink-muted">
              The request for <span class="font-semibold text-ink" x-text="selected?.name"></span> will be rejected.
              Please provide a clear reason so the requester understands the decision.
            </p>
          </div>
        </div>

        <form :action="rejectAction" method="POST" @submit="submitting = true" class="mt-5">
          @csrf
          @method('PATCH')
          <input type="hidden" name="request_id" :value="selected?.id">

          <label for="reject-reason" class="block text-caption font-semibold text-ink">Reason for rejection</label>
          <textarea id="reject-reason" name="reason" rows="4" required minlength="5" maxlength="255"
            x-model="rejectReason" @input="rejectError = null"
            placeholder="Explain why this request cannot be approved..."
            class="mt-2 w-full rounded-lg border border-hairline bg-canvas px-3 py-2 text-caption text-ink placeholder:text-ink-faint focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"></textarea>

          <div class="mt-1.5 flex items-center justify-between gap-3 text-small text-ink-faint">
            <span>Minimum 5 characters.</span>
            <span x-text="`${rejectReason.length}/255`"></span>
          </div>

          <p x-show="rejectError" x-cloak class="mt-2 text-caption text-accent-red" x-text="rejectError"></p>

          <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <button type="button" @click="rejectOpen = false" :disabled="submitting"
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
  </section>
@endsection
