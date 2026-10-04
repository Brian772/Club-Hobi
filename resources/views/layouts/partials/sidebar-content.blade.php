<div class="flex flex-col h-full max-h-dvh w-full overflow-hidden justify-between">
  {{-- Logo --}}
  <div class="shrink-0 hidden lg:flex items-start mx-4 gap-2 pt-2 border-b border-hairline">
    <img src="{{ asset('images/orbii-v2.svg') }}" alt="Orbii Logo" height="53" class="h-12.5 w-max object-contain">
  </div>

  {{-- Navigation --}}
  <nav
    class="flex-1 min-h-0 flex flex-col items-start lg:mt-2 mx-2 md:px-2 space-y-1 bg-canvas overflow-y-auto scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-hairline scrollbar-track-transparent">
    @php
      $navItems = [
          ['label' => 'Home', 'route' => 'dashboard', 'icon' => 'home', 'match' => ['dashboard']],
          ['label' => 'Club', 'route' => 'clubs.index', 'icon' => 'folder', 'match' => ['clubs.*']],
          ['label' => 'Pesan', 'route' => 'messages.index', 'icon' => 'chat', 'match' => ['messages.*']],
          [
              'label' => 'Notification',
              'route' => 'notifications.index',
              'icon' => 'notif',
              'match' => ['notifications.*'],
          ],
          ['label' => 'Settings', 'route' => 'settings.index', 'icon' => 'cog', 'match' => ['settings.*']],
      ];
      if (Auth::user()->role_global === 'admin') {
          $navAdminItems = [
              ['label' => 'Overview', 'route' => 'admin.overview', 'icon' => 'overview', 'match' => ['admin.overview']],
              [
                  'label' => 'User Management',
                  'route' => 'admin.user-management',
                  'icon' => 'user',
                  'match' => ['admin.user-management*'],
              ],
              [
                  'label' => 'Club Management',
                  'route' => 'admin.club-management',
                  'icon' => 'blocks',
                  'match' => ['admin.club-management*'],
              ],
              [
                  'label' => 'Club Request',
                  'route' => 'admin.clubs.request',
                  'icon' => 'request',
                  'match' => ['admin.clubs.request*'],
              ],
              [
                  'label' => 'Moderation',
                  'route' => 'admin.moderation',
                  'icon' => 'moderation',
                  'match' => ['admin.moderation*'],
              ],
              [
                  'label' => 'Audit Logs',
                  'route' => 'admin.audit-logs',
                  'icon' => 'audit-logs',
                  'match' => ['admin.audit-logs*'],
              ],
          ];
      }
    @endphp
    @foreach ($navItems as $item)
      @php $active = request()->routeIs(...$item['match']); @endphp
      <a href="{{ route($item['route']) }}"
        class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-all w-full duration-150
                      {{ $active ? 'text-primary font-semibold bg-primary/10' : 'hover:text-primary hover:translate-x-1' }}">
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
            class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-all w-full duration-150
                      {{ $activeAdmin ? 'text-primary font-semibold bg-primary/10' : 'hover:text-primary hover:translate-x-1' }}">
            @include('layouts.partials.icons.' . $adminItem['icon'])
            {{ $adminItem['label'] }}
          </a>
        @endforeach
      @endif
    @endforeach
  </nav>

  {{-- User profile bawah --}}
  <div class="shrink-0 bottom-0 w-full border-t border-hairline rounded-2xl px-4 py-4">
    <div class="flex items-center gap-3">
      @if (auth()->user()->avatar_full_url)
        <img src="{{ auth()->user()->avatar_full_url }}" alt="{{ auth()->user()->name }}"
          class="rounded-full size-9 object-cover"
          onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
        <div
          class="hidden size-9 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
          {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
        </div>
      @else
        <div
          class="size-9 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
          {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
        </div>
      @endif
      <span class="text-sm font-semibold text-neutral-900">{{ auth()->user()->name }}</span>
    </div>
  </div>
</div>
