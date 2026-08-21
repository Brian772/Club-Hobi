<header
  class="sticky top-4 z-30 flex items-center justify-between gap-4 border border-hairline bg-canvas/40 backdrop-blur px-4 py-3 inset-x-4 rounded-lg lg:hidden">
  <button @click="sidebarOpen = true" class="text-ink" aria-label="Buka menu">
    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
  </button>

  <button @click="notifOpen = true" class="relative text-neutral-700" aria-label="Buka notifikasi">
    <svg xmlns="http://www.w3.org/2000/svg" width="1.25em" height="1.25em" viewBox="0 0 24 24">
      <path d="M0 0h24v24H0z" fill="none" />
      <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M10.268 21a2 2 0 0 0 3.464 0m-10.47-5.674A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326" />
    </svg>


    {{-- @if ($unreadCount > 0)
      <span
        class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-red-600 text-white text-[10px] font-semibold flex items-center justify-center">
        {{ $unreadCount }}
      </span>
    @endif --}}
  </button>
</header>

<div class="hidden lg:flex justify-end px-8 pt-6">
  <button @click="notifOpen = true" class="relative text-neutral-500 hover:text-canvas hover:bg-primary p-1"
    aria-label="Buka notifikasi">
    <svg xmlns="http://www.w3.org/2000/svg" width="1.25em" height="1.25em" viewBox="0 0 24 24">
      <path d="M0 0h24v24H0z" fill="none" />
      <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M10.268 21a2 2 0 0 0 3.464 0m-10.47-5.674A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326" />
    </svg>


    {{-- @if ($unreadCount > 0)
      <span
        class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-red-600 text-white text-[10px] font-semibold flex items-center justify-center">
        {{ $unreadCount }}
      </span>
    @endif --}}
  </button>
</div>
