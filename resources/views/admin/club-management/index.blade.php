@extends('layouts.app')

@section('title', 'Orbii | Club Management')

@section('content')
  @php
    $totalMembers = $clubs->sum('members_count');
    $publicClubs = $clubs->where('visibility', 'public')->count();
    $privateClubs = $clubs->where('visibility', 'private')->count();
    $activeCategories = $clubs->pluck('hobby_id')->filter()->unique()->count();
    $categoryCounts = $clubs->groupBy('hobby_id')->map->count();

    $clubsData = $clubs
        ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
        ->values()
        ->map(function ($club) {
            $ownerName = $club->creator?->name ?? 'Unknown owner';

            return [
                'id' => $club->id,
                'name' => $club->name,
                'description' => $club->description ?? '',
                'category' => $club->hobby?->name ?? 'Uncategorized',
                'category_id' => (string) $club->hobby_id,
                'owner' => $ownerName,
                'owner_initial' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($ownerName, 0, 1)),
                'initial' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($club->name ?? '?', 0, 1)),
                'members' => (int) $club->members_count,
                'visibility' => $club->visibility ?? 'public',
                'cover' => $club->cover_url ? Storage::url($club->cover_url) : null,
                'created' => $club->created_at?->format('d M Y'),
                'url' => route('admin.club-management.show', $club->id),
            ];
        })
        ->all();
  @endphp

  <section class="mx-auto" x-data="{
    search: '',
    searchInput: '',
    searching: false,
    searchTimer: null,
    category: 'all',
    visibility: 'all',
    clubs: @js($clubsData),
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
      this.visibility = 'all';
    },
    get hasFilters() {
      return this.search !== '' || this.category !== 'all' || this.visibility !== 'all';
    },
    get filteredClubs() {
      const query = this.search.trim().toLowerCase();

      return this.clubs.filter((club) => {
        const matchesCategory = this.category === 'all' || club.category_id === this.category;
        const matchesVisibility = this.visibility === 'all' || club.visibility === this.visibility;
        const matchesSearch = query === '' || [club.name, club.description, club.owner, club.category]
          .some((value) => (value || '').toLowerCase().includes(query));

        return matchesCategory && matchesVisibility && matchesSearch;
      });
    }
  }">
    <header class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-ink">Club Management</h1>
        <p class="mt-1 max-w-2xl text-body-mid text-ink-muted">
          Browse, search, and manage every hobby club on Orbii from one place.
        </p>
      </div>

      <a href="{{ route('admin.clubs.request') }}"
        class="inline-flex w-max items-center gap-2 rounded-md border border-hairline bg-canvas px-3.5 py-2 text-caption font-semibold text-ink transition-colors duration-150 hover:border-primary/40 hover:text-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-inbox">
          <polyline points="22 12 16 12 14 15 10 15 8 12 2 12" />
          <path
            d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" />
        </svg>
        Review club requests
      </a>
    </header>

    {{-- Statistics --}}
    <section class="grid grid-cols-2 gap-2 lg:grid-cols-4 lg:gap-4">
      <div class="rounded-lg border border-hairline bg-canvas-soft p-4 transition-shadow duration-150 hover:shadow-sm">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-caption text-ink-muted">Total clubs</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ number_format($clubs->count()) }}</p>
          </div>
          <span class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-blocks">
              <rect width="7" height="7" x="14" y="3" rx="1" />
              <path d="M10 21V8a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1H3" />
            </svg>
          </span>
        </div>
        <p class="mt-2 text-small text-ink-faint">Across {{ $hobbies->count() }} categories</p>
      </div>

      <div class="rounded-lg border border-hairline bg-canvas-soft p-4 transition-shadow duration-150 hover:shadow-sm">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-caption text-ink-muted">Total members</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ number_format($totalMembers) }}</p>
          </div>
          <span class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent-teal/10 text-accent-teal">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-users">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
          </span>
        </div>
        <p class="mt-2 text-small text-ink-faint">Joined across all clubs</p>
      </div>

      <div class="rounded-lg border border-hairline bg-canvas-soft p-4 transition-shadow duration-150 hover:shadow-sm">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-caption text-ink-muted">Active categories</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ number_format($activeCategories) }}</p>
          </div>
          <span class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent-purple/20 text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-tags">
              <path d="m15 5 6.3 6.3a2.4 2.4 0 0 1 0 3.4L17 19" />
              <path d="M9.586 5.586A2 2 0 0 0 8.172 5H3a1 1 0 0 0-1 1v5.172a2 2 0 0 0 .586 1.414L8.29 18.29a2.426 2.426 0 0 0 3.42 0l3.58-3.58a2.426 2.426 0 0 0 0-3.42z" />
              <circle cx="6.5" cy="9.5" r=".5" fill="currentColor" />
            </svg>
          </span>
        </div>
        <p class="mt-2 text-small text-ink-faint">With at least one club</p>
      </div>

      <div class="rounded-lg border border-hairline bg-canvas-soft p-4 transition-shadow duration-150 hover:shadow-sm">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-caption text-ink-muted">Private clubs</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ number_format($privateClubs) }}</p>
          </div>
          <span class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent-orange/10 text-accent-orange">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-lock">
              <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
              <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
          </span>
        </div>
        <p class="mt-2 text-small text-ink-faint">Public clubs: {{ number_format($publicClubs) }}</p>
      </div>
    </section>

    <main class="mt-4 rounded-lg border border-hairline bg-canvas p-4 lg:p-5">
      {{-- Search and filters --}}
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-center lg:max-w-3xl">
          <div class="relative w-full sm:max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-muted">
              <path d="m21 21-4.34-4.34" />
              <circle cx="11" cy="11" r="8" />
            </svg>
            <input type="search" :value="searchInput" @input="updateSearch($event.target.value)"
              placeholder="Search by club, owner, or category" aria-label="Search clubs"
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

          <select x-model="visibility" aria-label="Filter by visibility"
            class="w-full rounded-lg border border-hairline bg-canvas px-3 pr-10 py-2 text-caption text-ink focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 sm:w-auto">
            <option value="all">All visibility</option>
            <option value="public">Public</option>
            <option value="private">Private</option>
          </select>
        </div>

        <div class="flex flex-wrap items-center gap-3 text-caption text-ink-muted">
          <span aria-live="polite" x-text="`${filteredClubs.length} of ${clubs.length} clubs`"></span>
          <button type="button" x-show="hasFilters" x-cloak @click="resetFilters()"
            class="font-semibold text-primary hover:underline hover:underline-offset-2">
            Reset filters
          </button>
        </div>
      </div>

      {{-- Category navigation --}}
      <div class="mt-4 border-t border-hairline pt-4">
        <div class="flex flex-wrap items-center gap-2" role="group" aria-label="Filter clubs by category">
          <button type="button" @click="category = 'all'"
            :class="category === 'all' ?
                'border-primary bg-primary text-white' :
                'border-hairline bg-canvas text-ink-muted hover:border-primary/40 hover:text-primary'"
            class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-caption font-medium transition-colors duration-150">
            All clubs
            <span
              :class="category === 'all' ? 'bg-white/20 text-white' : 'bg-canvas-soft text-ink-muted'"
              class="rounded-full px-1.5 py-0.5 text-small font-semibold">{{ $clubs->count() }}</span>
          </button>

          @foreach ($hobbies as $hobby)
            <button type="button" @click="category = '{{ $hobby->id }}'"
              :class="category === '{{ $hobby->id }}' ?
                  'border-primary bg-primary text-white' :
                  'border-hairline bg-canvas text-ink-muted hover:border-primary/40 hover:text-primary'"
              class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-caption font-medium transition-colors duration-150">
              {{ $hobby->name }}
              <span
                :class="category === '{{ $hobby->id }}' ? 'bg-white/20 text-white' : 'bg-canvas-soft text-ink-muted'"
                class="rounded-full px-1.5 py-0.5 text-small font-semibold">{{ $categoryCounts[$hobby->id] ?? 0 }}</span>
            </button>
          @endforeach
        </div>
      </div>

      {{-- Results --}}
      <div class="mt-5">
        <div class="mb-3 flex items-center justify-between gap-3">
          <h2 class="text-body-mid font-semibold text-ink">Clubs</h2>
          <span class="text-caption text-ink-muted" x-show="hasFilters" x-cloak>Filtered results</span>
        </div>

        <div x-show="filteredClubs.length > 0" x-cloak class="grid grid-cols-1 gap-3 xl:grid-cols-2">
          <template x-for="club in filteredClubs" :key="club.id">
            <article
              class="group flex flex-col overflow-hidden rounded-lg border border-hairline bg-canvas transition-shadow duration-200 hover:shadow-md sm:flex-row">
              <a :href="club.url" class="relative block h-40 w-full shrink-0 overflow-hidden bg-canvas-light sm:min-h-36 sm:w-44"
                :aria-label="`View ${club.name}`">
                <template x-if="club.cover">
                  <img :src="club.cover" :alt="`${club.name} cover`" loading="lazy"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
                </template>
                <template x-if="!club.cover">
                  <div class="flex h-full min-h-36 w-full items-center justify-center bg-canvas-light">
                    <span class="text-heading-2 font-bold text-ink-faint" x-text="club.initial"></span>
                  </div>
                </template>
              </a>

              <div class="flex min-w-0 flex-1 flex-col p-4">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <a :href="club.url"
                      class="block truncate text-body-mid font-semibold text-ink transition-colors duration-150 hover:text-primary"
                      x-text="club.name"></a>
                    <div class="mt-1.5 flex flex-wrap items-center gap-2">
                      <span
                        class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-overline font-semibold uppercase tracking-wide text-primary"
                        x-text="club.category"></span>
                      <span
                        class="inline-flex items-center gap-1 rounded-full border border-hairline px-2 py-0.5 text-overline font-semibold uppercase tracking-wide text-ink-muted">
                        <template x-if="club.visibility === 'private'">
                          <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                          </svg>
                        </template>
                        <template x-if="club.visibility !== 'private'">
                          <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                            <path d="M2 12h20" />
                          </svg>
                        </template>
                        <span x-text="club.visibility"></span>
                      </span>
                    </div>
                  </div>

                  <div class="relative shrink-0" x-data="{ menuOpen: false }">
                    <button type="button" @click="menuOpen = !menuOpen" @keydown.escape.window="menuOpen = false"
                      aria-label="Club actions"
                      class="flex size-8 items-center justify-center rounded-full text-ink-muted transition-colors duration-150 hover:bg-canvas-soft hover:text-ink focus:outline-none focus:ring-2 focus:ring-primary/20">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="1" />
                        <circle cx="12" cy="5" r="1" />
                        <circle cx="12" cy="19" r="1" />
                      </svg>
                    </button>

                    <div x-show="menuOpen" x-cloak @click.outside="menuOpen = false"
                      x-transition:enter="transition ease-out duration-150"
                      x-transition:enter-start="opacity-0 -translate-y-1"
                      x-transition:enter-end="opacity-100 translate-y-0"
                      x-transition:leave="transition ease-in duration-100"
                      x-transition:leave-start="opacity-100 translate-y-0"
                      x-transition:leave-end="opacity-0 -translate-y-1"
                      class="absolute right-0 z-30 mt-1 w-40 rounded-lg border border-hairline bg-canvas p-1.5 shadow-lg">
                      <a :href="club.url" @click="menuOpen = false"
                        class="flex items-center gap-2 rounded-md px-3 py-2 text-caption text-ink transition-colors hover:bg-canvas-soft">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round">
                          <path
                            d="m6 14 1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2" />
                        </svg>
                        Lihat club
                      </a>
                    </div>
                  </div>
                </div>

                <p class="mt-2 line-clamp-2 text-caption text-ink-muted"
                  x-text="club.description || 'No description provided.'"></p>

                <div
                  class="mt-auto flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-hairline pt-3 text-caption text-ink-muted">
                  <span class="flex min-w-0 items-center gap-1.5">
                    <span
                      class="flex size-5 shrink-0 items-center justify-center rounded-full bg-primary/10 text-[10px] font-semibold text-primary"
                      x-text="club.owner_initial"></span>
                    <span class="truncate text-ink" x-text="club.owner"></span>
                  </span>
                  <span class="flex shrink-0 items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                      fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                      stroke-linejoin="round">
                      <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                      <circle cx="9" cy="7" r="4" />
                      <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                      <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    <span><span x-text="club.members"></span> members</span>
                  </span>
                  <span x-show="club.created" class="flex shrink-0 items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                      fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                      stroke-linejoin="round">
                      <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                      <line x1="16" x2="16" y1="2" y2="6" />
                      <line x1="8" x2="8" y1="2" y2="6" />
                      <line x1="3" x2="21" y1="10" y2="10" />
                    </svg>
                    <span x-text="club.created"></span>
                  </span>
                </div>
              </div>
            </article>
          </template>
        </div>

        {{-- Empty state --}}
        <div x-show="filteredClubs.length === 0" x-cloak
          class="flex flex-col items-center justify-center rounded-lg border border-dashed border-hairline bg-canvas-soft px-6 py-14 text-center">
          <span class="flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.3-4.3" />
            </svg>
          </span>
          <h3 class="mt-4 text-title text-ink">No clubs found</h3>
          <p class="mt-1 max-w-sm text-caption text-ink-muted">
            No clubs match your current search and filters. Try a different keyword or reset the filters.
          </p>
          <button type="button" x-show="hasFilters" @click="resetFilters()"
            class="mt-5 inline-flex items-center gap-2 rounded-md bg-primary/10 px-4 py-2 text-caption font-semibold text-primary transition-colors duration-150 hover:bg-primary hover:text-white">
            Reset filters
          </button>
        </div>
      </div>
    </main>
  </section>
@endsection
