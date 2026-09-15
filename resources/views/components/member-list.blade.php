<div
  class="flex flex-row w-full lg:px-4 py-2 items-center justify-between gap-4 mx-auto border border-canvas hover:border-hairline rounded-lg transition-colors duration-300">
  <div class="flex flex-row gap-2 items-center justify-start w-max">
    <div class="min-w-10 mr-2">
      <img src="{{ $member->user->avatar_full_url }}" alt="{{ $member->user->name }}"
        class="rounded-full w-10 h-10 object-cover">
    </div>
    <div class="flex flex-col gap-2">
      <h3 class="text-body-mid font-semibold text-ink">{{ $member->user->name }}
        @if ($member->user->role_global === 'admin')
          <span
            class="text-overline text-primary bg-primary/10 rounded-full px-2 py-1 border border-primary font-semibold">Admin</span>
        @endif
      </h3>
      <p class="text-caption text-ink-muted"><span class="text-ink font-semibold">Hobi:
        </span>{{ implode(', ', $member->user->interest_array ?? []) }}</p>
    </div>
  </div>

  <div class="flex flex-row gap-2 items-center w-max">
    <span class="text-caption text-ink-muted">{{ $member->role }}</span>
    @if ($member->user->id !== Auth::user()->id)
      <div x-data="{ MenuOpen: false }">
        <button type="button" @click="MenuOpen = true"
          class="text-ink-muted text-body-mid rounded-full p-2 hover:bg-hairline">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-ellipsis-vertical">
            <circle cx="12" cy="12" r="1" />
            <circle cx="12" cy="5" r="1" />
            <circle cx="12" cy="19" r="1" />
          </svg>
        </button>

        <div x-show="MenuOpen" x-cloak @keydown.escape.window="MenuOpen = false">
          <div x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
            @click.outside="MenuOpen = false" @click="MenuOpen = false"
            class="absolute z-50 right-5 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
            <a href="{{ route('profile.show', ['user' => $member->user->id]) }}"
              class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-user-round">
                <circle cx="12" cy="8" r="5" />
                <path d="M20 21a8 8 0 0 0-16 0" />
              </svg>
              Lihat Profil
            </a>
            <a href="{{ route('messages.index', ['conversation' => $member->user->id]) }}"
              class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-message-circle-icon lucide-message-circle">
                <path
                  d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719" />
              </svg>
              Kirim Pesan
            </a>
            <div class="h-px border-b border-hairline my-2"></div>
            <button type="button"
              @click="
                openReport(
                  'user',
                  '{{ $member->user->id }}',
                  '{{ $member->user->id }}',
                  '{{ route('reports.store') }}',
                  @js([
                    'name' => $member->user->name,
                    'avatar' => $member->user->avatar_full_url ?? asset('images/default-avatar.png'),
                    'joined' => $member->user->created_at->format('d M Y'),
                  ])
                )"
              class="flex flex-row gap-2 items-center w-full cursor-pointer px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-flag">
                <path
                  d="M4 22V4a1 1 0 0 1 .4-.8A6 6 0 0 1 8 2c3 0 5 2 7.333 2q2 0 3.067-.8A1 1 0 0 1 20 4v10a1 1 0 0 1-.4.8A6 6 0 0 1 16 16c-3 0-5-2-8-2a6 6 0 0 0-4 1.528" />
              </svg>
              Laporkan Pengguna
            </button>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>
