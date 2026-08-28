<div class="flex flex-col h-full w-full justify-between">
  <div class="flex flex-col gap-2">
    {{-- Logo --}}
    <div class="hidden lg:flex items-start mx-4 gap-2 pt-2 border-b border-hairline">
      <img src="{{ asset('images/orbii-v2.svg') }}" alt="Orbii Logo" height="53" class="h-12.5 w-max object-contain">
    </div>

    <nav class="flex flex-col items-start mx-2 md:px-2 space-y-1 bg-canvas">
      @php
        $navItems = [
            ['label' => 'Home', 'route' => 'dashboard', 'icon' => 'home'],
            ['label' => 'Notification', 'route' => 'notifications.index', 'icon' => 'notif'],
            ['label' => 'Pesan', 'route' => 'messages.index', 'icon' => 'chat'],
            ['label' => 'Club', 'route' => 'clubs.index', 'icon' => 'folder'],
            ['label' => 'Settings', 'route' => 'settings.index', 'icon' => 'cog'],
        ];
      @endphp
      @foreach ($navItems as $item)
        @php $active = request()->routeIs($item['route']); @endphp
        <a href="{{ route($item['route']) }}"
          class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-colors w-full
                      {{ $active ? 'bg-canvas text-primary font-bold' : 'hover:bg-primary/10 hover:text-primary' }}">
          @if ($active)
            <span class="absolute left-0 w-1 h-6 rounded-r-full bg-primary"></span>
          @endif
          @include('layouts.partials.icons.' . $item['icon'])
          {{ $item['label'] }}
        </a>
      @endforeach
    </nav>
  </div>

  {{-- User profile bawah --}}
  <div class="border-t border-neutral-200 mt-4 mx-4 px-4 py-4">
    <div class="flex items-center gap-3">
      <img src="{{ auth()->user()->avatar_full_url ?? asset('images/default-avatar.png') }}"
        alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover">
      <span class="text-sm font-semibold text-neutral-900">{{ auth()->user()->name }}</span>
    </div>
  </div>
</div>
