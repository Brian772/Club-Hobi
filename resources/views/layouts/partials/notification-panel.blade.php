{{-- Mobile: overlay + backdrop --}}
<div x-show="notifOpen" x-cloak class="fixed inset-0 z-50 lg:hidden">
  <div 
    x-transition:enter="transition-opacity ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="notifOpen = false"
    class="fixed inset-0 bg-black/30"></div>

  <div
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    @keydown.escape.window="notifOpen = false"
    class="fixed inset-y-0 right-0 w-full max-w-sm bg-white shadow-xl flex flex-col"
  >
    <div class="flex items-center justify-between px-6 py-5 border-b border-neutral-200">
      <button type="button" @click="notifOpen = false"
        class="flex items-center gap-2 px-4 py-2 rounded-md text-ink hover:text-white hover:bg-red-600 text-sm transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Close
      </button>
    </div>

    <div class="flex items-center justify-between px-6 pt-5">
      <h2 class="text-xl font-bold text-neutral-900">Notifikasi</h2>
      <a href="{{ route('notifications.index') }}" class="text-sm text-neutral-400 hover:text-neutral-700">See More
        →</a>
    </div>

    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5">
      @forelse ($notifications ?? [] as $notif)
        <div class="flex gap-3 {{ $notif->is_read ? 'opacity-60' : '' }}">
          <div class="mt-0.5 shrink-0">
            @switch($notif->type)
              @case('message')
                @include('layouts.partials.icons.chat')
              @break

              @case('comment')
                @include('layouts.partials.icons.chat')
              @break

              @case('report')
              @case('account_status')
                @include('layouts.partials.icons.bell')
              @break

              @default
                @include('layouts.partials.icons.bell')
            @endswitch
          </div>
          <div>
            <p class="text-sm font-semibold text-neutral-900">{{ $notif->title }}</p>
            <p class="text-sm text-neutral-600">{{ $notif->content }}</p>
            <p class="text-xs text-neutral-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
          </div>
        </div>
        @empty
          <p class="text-sm text-neutral-400 text-center py-8">Belum ada notifikasi.</p>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Desktop: docked panel (tanpa backdrop) --}}
  <aside 
    x-show="notifOpen"
    x-cloak x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="translate-x-full opacity-0" 
    x-transition:enter-end="translate-x-0 opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="translate-x-0 opacity-100"
    x-transition:leave-end="translate-x-full opacity-0"
    @keydown.escape.window="notifOpen = false"
    class="hidden lg:flex fixed inset-y-4 right-4 z-40 h-screen w-[24rem] rounded-md bg-white border-l border-neutral-200 shadow-xl flex-col"
  >
    <div class="flex items-center justify-between px-6 py-5 border-b border-neutral-200">
      <button type="button" @click="notifOpen = false"
        class="flex items-center gap-2 px-4 py-2 rounded-md text-ink hover:text-white hover:bg-red-600 text-sm transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Close
      </button>
    </div>

    <div class="flex items-center justify-between px-6 pt-5">
      <h2 class="text-xl font-bold text-neutral-900">Notifikasi</h2>
      <a href="{{ route('notifications.index') }}" class="text-sm text-neutral-400 hover:text-neutral-700">See More →</a>
    </div>

    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5">
      @forelse ($notifications ?? [] as $notif)
        <div class="flex gap-3 {{ $notif->is_read ? 'opacity-60' : '' }}">
          <div class="mt-0.5 shrink-0">
            @switch($notif->type)
              @case('message')
                @include('layouts.partials.icons.chat')
              @break

              @case('comment')
                @include('layouts.partials.icons.chat')
              @break

              @case('report')
              @case('account_status')
                @include('layouts.partials.icons.bell')
              @break

              @default
                @include('layouts.partials.icons.bell')
            @endswitch
          </div>
          <div>
            <p class="text-sm font-semibold text-neutral-900">{{ $notif->title }}</p>
            <p class="text-sm text-neutral-600">{{ $notif->content }}</p>
            <p class="text-xs text-neutral-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
          </div>
        </div>
        @empty
          <p class="text-sm text-neutral-400 text-center py-8">Belum ada notifikasi.</p>
        @endforelse
      </div>
    </aside>
