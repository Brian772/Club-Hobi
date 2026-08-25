<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Club Hobi</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @yield('styles')
</head>

<body class="bg-canvas-soft">

  @if (Route::is('login') || Route::is('register') || Route::is('home'))
    <div class="flex flex-col p-6 lg:p-8 min-h-dvh justify-center">
      <x-alert />

      @yield('content')
    </div>
  @else
    <div x-data="{ sidebarOpen: false, notifOpen: false }" class="min-h-dvh p-6 flex flex-col lg:flex-row lg:p-4.25">

      {{-- sidebar desktop --}}
      <aside
        class="hidden lg:fixed lg:inset-y-4 lg:left-4 lg:rounded-lg lg:z-30 lg:flex lg:h-[calc(100vh-2rem)] lg:shrink-0 lg:w-60 lg:overflow-hidden lg:border lg:border-hairline lg:bg-canvas">
        @include('layouts.partials.sidebar-content')
      </aside>

      {{-- sidebar mobile --}}
      <div x-show="sidebarOpen" x-cloak @keydown.escape.window="sidebarOpen = false"
        class="lg:hidden fixed inset-0 z-50">
        {{-- background gelap --}}
        <div
          x-transition:enter="transition-opacity ease-out duration-200"
          x-transition:enter-start="opacity-0"
          x-transition:enter-end="opacity-100"
          x-transition:leave="transition-opacity ease-in duration-200"
          x-transition:leave-start="opacity-100"
          x-transition:leave-end="opacity-0"
          @click="sidebarOpen = false"
          class="fixed inset-0 z-40 bg-black/30"></div>

        {{-- panel --}}
        <div
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="-translate-x-full"
          x-transition:enter-end="translate-x-0"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="translate-x-0"
          x-transition:leave-end="-translate-x-full"
          @click.stop
          class="fixed top-16 left-5 h-max rounded-lg z-50 w-60 bg-canvas border border-hairline overflow-hidden">
          <div class="flex items-center justify-start p-4">
            <button type="button" @click="sidebarOpen = false" aria-label="Tutup Menu" class="hover:bg-canvas">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M4.45742 4.43968C4.59672 4.30029 4.76212 4.18971 4.94417 4.11427C5.12622 4.03883 5.32135 4 5.51842 4C5.71548 4 5.91061 4.03883 6.09266 4.11427C6.27471 4.18971 6.44012 4.30029 6.57942 4.43968L11.8824 9.74368L17.1854 4.44068C17.4667 4.15929 17.8482 4.00115 18.2461 4.00105C18.6439 4.00096 19.0255 4.15892 19.3069 4.44018C19.5883 4.72144 19.7464 5.10297 19.7465 5.50083C19.7466 5.89868 19.5887 6.28029 19.3074 6.56168L14.0034 11.8657L19.3074 17.1687C19.4467 17.308 19.5571 17.4734 19.6325 17.6554C19.7078 17.8375 19.7466 18.0325 19.7465 18.2295C19.7465 18.4265 19.7076 18.6216 19.6322 18.8036C19.5568 18.9856 19.4462 19.1509 19.3069 19.2902C19.1676 19.4294 19.0022 19.5399 18.8202 19.6153C18.6381 19.6906 18.4431 19.7294 18.2461 19.7293C18.0491 19.7293 17.854 19.6904 17.672 19.615C17.49 19.5395 17.3247 19.429 17.1854 19.2897L11.8824 13.9857L6.57942 19.2887C6.44111 19.432 6.27565 19.5464 6.09268 19.6251C5.90971 19.7038 5.7129 19.7452 5.51374 19.7471C5.31457 19.7489 5.11703 19.711 4.93265 19.6357C4.74827 19.5603 4.58074 19.449 4.43984 19.3083C4.29894 19.1675 4.18748 19.0001 4.11197 18.8158C4.03646 18.6315 3.99841 18.434 4.00005 18.2348C4.00169 18.0356 4.04297 17.8388 4.1215 17.6557C4.20002 17.4727 4.31422 17.3071 4.45742 17.1687L9.76242 11.8637L4.45742 6.56068C4.17652 6.27943 4.01874 5.89818 4.01874 5.50068C4.01874 5.10318 4.17652 4.72093 4.45742 4.43968Z"
                  fill="black" />
              </svg>
            </button>
          </div>
          <div>
            @include('layouts.partials.sidebar-content')
          </div>
        </div>
      </div>

      <div
        class="flex-1 min-w-0 w-full flex flex-col rounded-lg h-[calc(100vh-34px)] overflow-hidden lg:pl-61.25 transition-[padding] duration-200"
        :class="notifOpen ? 'lg:pr-[24.3rem]' : 'lg:pr-0'">
        @include('layouts.partials.topbar')

        <main class="pt-12 p-6 lg:p-8 lg:bg-canvas rounded-lg flex-1 min-h-0 overflow-y-auto lg:border lg:border-hairline">
          <x-alert-account-status />
          <x-alert />
          @yield('content')
        </main>
      </div>
      @include('layouts.partials.notification-panel')
    </div>
  @endif
  @stack('scripts')
</body>

</html>
