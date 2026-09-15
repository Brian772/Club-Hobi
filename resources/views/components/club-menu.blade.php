<div x-data="{ MenuOpen: false }">
  <button type="button" @click="MenuOpen = true"
    class="text-ink-muted text-body-mid rounded-full p-2 hover:bg-hairline lg:mr-8">
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
      @click.outside="MenuOpen = false"
      class="absolute z-50 right-5 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
      <a href="{{ route('admin.club-management.show', $club->id) }}"
        class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-folder-open">
          <path
            d="m6 14 1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2" />
        </svg>
        Lihat Club
      </a>
    </div>
  </div>
</div>
