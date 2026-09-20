<div class="flex flex-col h-full w-full justify-between">
  <div class="flex flex-col gap-2">
    {{-- Logo --}}
    <div class="hidden lg:flex items-start mx-4 gap-2 pt-2 border-b border-hairline">
      <img src="{{ asset('images/orbii-v2.svg') }}" alt="Orbii Logo" height="53" class="h-12.5 w-max object-contain">
    </div>

    <nav class="flex flex-col items-start mx-2 md:px-2 space-y-1 bg-canvas">
      @php
        $navItems = [
            ['label' => 'Home', 'route' => 'dashboard', 'icon' => 'home', 'match' => ['dashboard']],
            ['label' => 'Club', 'route' => 'clubs.index', 'icon' => 'folder', 'match' =>['clubs.*']],
            ['label' => 'Pesan', 'route' => 'messages.index', 'icon' => 'chat', 'match' => ['messages.*']],
            ['label' => 'Notification', 'route' => 'notifications.index', 'icon' => 'notif', 'match' => ['notifications.*']],
            ['label' => 'Settings', 'route' => 'settings.index', 'icon' => 'cog', 'match' => ['settings.*']],
        ];
        if (Auth::user()->role_global === 'admin') {
            $navAdminItems = [
              ['label' => 'Overview', 'route' => 'admin.overview', 'icon' => 'overview', 'match' => ['admin.overview']],
              ['label' => 'User Management', 'route' => 'admin.user-management', 'icon' => 'user', 'match' => ['admin.user-management*']],
              ['label' => 'Club Management', 'route' => 'admin.club-management', 'icon' => 'blocks', 'match' => ['admin.club-management*']],
              ['label' => 'Club Request', 'route' => 'admin.clubs.request', 'icon' => 'request', 'match' => ['admin.clubs.request*']],
              ['label' => 'Moderation', 'route' => 'admin.moderation', 'icon' => 'moderation', 'match' => ['admin.moderation*']],
              ['label' => 'Audit Logs', 'route' => 'admin.audit-logs', 'icon' => 'audit-logs', 'match' => ['admin.audit-logs*']],
            ];
        }
      @endphp
      @foreach ($navItems as $item)
        @php $active = request()->routeIs(...$item['match']); @endphp
        <a href="{{ route($item['route']) }}"
          class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-colors w-full
                      {{ $active ? 'bg-canvas text-primary font-bold' : 'hover:bg-primary/10 hover:text-primary' }}">
          @if ($active)
            <span class="absolute left-0 w-1 h-6 rounded-r-full bg-primary"></span>
          @endif
          @include('layouts.partials.icons.' . $item['icon'])
          {{ $item['label'] }}
        </a>

        @if (Auth::user()->role_global === 'admin' && $loop->last)
          <div class="border-t border-hairline w-full mt-4"></div>
          <span class="text-sm mx-2 mt-8 mb-2 font-semibold text-ink-muted select-none">Admin</span>

          @foreach ($navAdminItems as $adminItem)
            @php
              $activeAdmin = request()->routeIs(...$adminItem['match']);
            @endphp
            <a href="{{ route($adminItem['route']) }}"
              class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-colors w-full
                      {{ $activeAdmin ? 'bg-canvas text-primary font-bold' : 'hover:bg-primary/10 hover:text-primary' }}">
              @if ($activeAdmin)
                <span class="absolute left-0 w-1 h-6 rounded-r-full bg-primary"></span>
              @endif
              @include('layouts.partials.icons.' . $adminItem['icon'])
              {{ $adminItem['label'] }}
            </a>
          @endforeach
        @endif
      @endforeach
    </nav>
  </div>

  {{-- User profile bawah --}}
  <div class="border-t border-hairline mt-4 mx-4 px-4 py-4">
    <div class="flex items-center gap-3">
      <img src="{{ auth()->user()->avatar_full_url ?? asset('images/default-avatar.png') }}"
        alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover">
      <span class="text-sm font-semibold text-neutral-900">{{ auth()->user()->name }}</span>
    </div>
  </div>
</div>
