<div class="flex h-full w-full flex-col justify-between bg-white">
  <div class="flex flex-col gap-3 p-3">
    <div class="hidden lg:flex items-center justify-start rounded-xl border border-hairline bg-slate-50/80 px-3 py-3">
      <img src="{{ asset('images/orbii-v2.svg') }}" alt="Orbii Logo" class="h-10 w-auto object-contain">
    </div>

    <nav class="flex w-full flex-col gap-1.5">
      @php
        $navItems = [
            ['label' => 'Home', 'route' => 'dashboard', 'icon' => 'home'],
            ['label' => 'Club', 'route' => 'clubs.index', 'icon' => 'folder'],
            ['label' => 'Pesan', 'route' => 'messages.index', 'icon' => 'chat'],
            ['label' => 'Notification', 'route' => 'notifications.index', 'icon' => 'notif'],
            ['label' => 'Settings', 'route' => 'settings.index', 'icon' => 'cog'],
        ];

        $isAdmin = false;
        if (Auth::check() && Auth::user()->role_global === 'admin') {
            $isAdmin = true;
            $navAdminItems = [
              ['label' => 'Overview', 'route' => 'admin.overview', 'icon' => 'overview'],
              ['label' => 'User Management', 'route' => 'admin.user-management', 'icon' => 'user'],
              ['label' => 'Club Management', 'route' => 'admin.club-management', 'icon' => 'blocks'],
              ['label' => 'Club Request', 'route' => 'admin.clubs.request', 'icon' => 'request'],
              ['label' => 'Moderation', 'route' => 'admin.moderation', 'icon' => 'moderation'],
            ];
        }
      @endphp

      @foreach ($navItems as $item)
        @php $active = request()->routeIs($item['route']); @endphp
        <a href="{{ route($item['route']) }}"
          class="group relative flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
            {{ $active ? 'bg-primary/10 text-primary shadow-[inset_0_0_0_1px_rgba(71,108,255,0.08)]' : 'text-slate-700 hover:bg-slate-100 hover:text-primary' }}">
          @if ($active)
            <span class="absolute left-1.5 top-1/2 h-6 w-1.5 -translate-y-1/2 rounded-full bg-primary"></span>
          @endif
          <span class="flex h-6 w-6 items-center justify-center rounded-md {{ $active ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-600 group-hover:bg-primary/10 group-hover:text-primary' }}">
            @include('layouts.partials.icons.' . $item['icon'])
          </span>
          <span class="{{ $active ? 'font-semibold' : '' }}">{{ $item['label'] }}</span>
        </a>

        @if ($isAdmin && $loop->last)
          <div class="mt-2 border-t border-hairline pt-3"></div>
          <span class="mx-1 mb-1 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Admin</span>

          @foreach ($navAdminItems as $adminItem)
            @php
              $activeAdmin = request()->routeIs($adminItem['route']);
            @endphp
            <a href="{{ route($adminItem['route']) }}"
              class="group relative flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150
                {{ $activeAdmin ? 'bg-primary/10 text-primary shadow-[inset_0_0_0_1px_rgba(71,108,255,0.08)]' : 'text-slate-700 hover:bg-slate-100 hover:text-primary' }}">
              @if ($activeAdmin)
                <span class="absolute left-1.5 top-1/2 h-6 w-1.5 -translate-y-1/2 rounded-full bg-primary"></span>
              @endif
              <span class="flex h-6 w-6 items-center justify-center rounded-md {{ $activeAdmin ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-600 group-hover:bg-primary/10 group-hover:text-primary' }}">
                @include('layouts.partials.icons.' . $adminItem['icon'])
              </span>
              <span class="{{ $activeAdmin ? 'font-semibold' : '' }}">{{ $adminItem['label'] }}</span>
            </a>
          @endforeach
        @endif
      @endforeach
    </nav>
  </div>

  @if(Auth::check())
    <div class="border-t border-hairline p-3">
      <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-2.5 py-2.5">
        <img src="{{ auth()->user()->avatar_full_url ?? asset('images/default-avatar.svg') }}"
          alt="{{ auth()->user()->name }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-white shadow-sm">
        <div class="min-w-0">
          <p class="truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
          <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400">Member</p>
        </div>
      </div>
    </div>
  @endif
</div>
