<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Club Hobi</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: system-ui, -apple-system, sans-serif;
      background: #f4f5f7;
      color: #1f2937;
      min-height: 100vh;
    }

    nav {
      background: #1f2937;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    nav a {
      color: #f9fafb;
      text-decoration: none;
      margin-right: 1rem;
      font-size: 0.95rem;
    }

    nav a:hover {
      text-decoration: underline;
    }

    nav form button {
      background: #374151;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      cursor: pointer;
    }

    .container {
      max-width: 2000px;
      /* Diperbesar dari 720px */
      margin: 0rem auto;
      background: #fff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* Class khusus untuk halaman auth (login/register) */
    .auth-page .container {
      max-width: 100%;
      margin: 0;
      padding: 0;
      background: transparent;
      box-shadow: none;
      border-radius: 0;
      min-height: calc(100vh - 80px);
      /* Kurangi tinggi navbar */
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .alert {
      padding: 0.75rem 1rem;
      border-radius: 6px;
      margin-bottom: 1rem;
      font-size: 0.9rem;
    }

    .alert-success {
      background: #d1fae5;
      color: #065f46;
    }

    .alert-error {
      background: #fee2e2;
      color: #991b1b;
    }

    label {
      display: block;
      margin-bottom: 0.3rem;
      font-weight: 600;
      font-size: 0.9rem;
    }

    input,
    textarea,
    select {
      width: 100%;
      padding: 0.6rem;
      margin-bottom: 1rem;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 0.95rem;
    }

    button,
    .btn {
      display: inline-block;
      background: #2563eb;
      color: #fff;
      border: none;
      padding: 0.6rem 1.2rem;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.95rem;
      text-decoration: none;
    }

    button:hover,
    .btn:hover {
      background: #1d4ed8;
    }

    .btn-danger {
      background: #dc2626;
    }

    .btn-danger:hover {
      background: #b91c1c;
    }

    .error-text {
      color: #b91c1c;
      font-size: 0.8rem;
      margin-top: -0.7rem;
      margin-bottom: 0.8rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }

    th,
    td {
      text-align: left;
      padding: 0.6rem;
      border-bottom: 1px solid #e5e7eb;
      font-size: 0.9rem;
    }

    .actions a,
    .actions button {
      margin-right: 0.5rem;
      font-size: 0.85rem;
    }
  </style>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @yield('styles')
</head>

<body class="bg-canvas-soft">

  @if (Route::is('login') || Route::is('register'))
    <div class="flex flex-col p-6 md:p-8 min-h-dvh justify-center">
      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
      @endif

      @yield('content')
    </div>
  @else
    <div x-data="{ sidebarOpen: false, notifOpen: false }" class="min-h-dvh p-[24px] flex flex-col md:flex-row md:p-[32px]">

      {{-- sidebar desktop --}}
      <aside
        class="hidden md:fixed md:inset-y-4 md:left-4 md:rounded-lg md:z-30 md:flex md:h-dvh md:w-[240px] md:overflow-hidden md:border md:border-hairline md:bg-canvas">
        @include('layouts.partials.sidebar-content')
      </aside>

      {{-- sidebar mobile --}}
      <div x-show="sidebarOpen" x-cloak @keydown.escape.window="sidebarOpen = false"
        class="md:hidden fixed inset-0 z-50">
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
          class="fixed inset-y-4 left-4 h-max rounded-lg z-50 w-[240px] bg-canvas"
        >
          <div class="flex items-center justify-start p-4">
            <button type="button" @click="sidebarOpen = false" aria-label="Tutup Menu" class="hover:text-canvas hover:bg-primary">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M4.45742 4.43968C4.59672 4.30029 4.76212 4.18971 4.94417 4.11427C5.12622 4.03883 5.32135 4 5.51842 4C5.71548 4 5.91061 4.03883 6.09266 4.11427C6.27471 4.18971 6.44012 4.30029 6.57942 4.43968L11.8824 9.74368L17.1854 4.44068C17.4667 4.15929 17.8482 4.00115 18.2461 4.00105C18.6439 4.00096 19.0255 4.15892 19.3069 4.44018C19.5883 4.72144 19.7464 5.10297 19.7465 5.50083C19.7466 5.89868 19.5887 6.28029 19.3074 6.56168L14.0034 11.8657L19.3074 17.1687C19.4467 17.308 19.5571 17.4734 19.6325 17.6554C19.7078 17.8375 19.7466 18.0325 19.7465 18.2295C19.7465 18.4265 19.7076 18.6216 19.6322 18.8036C19.5568 18.9856 19.4462 19.1509 19.3069 19.2902C19.1676 19.4294 19.0022 19.5399 18.8202 19.6153C18.6381 19.6906 18.4431 19.7294 18.2461 19.7293C18.0491 19.7293 17.854 19.6904 17.672 19.615C17.49 19.5395 17.3247 19.429 17.1854 19.2897L11.8824 13.9857L6.57942 19.2887C6.44111 19.432 6.27565 19.5464 6.09268 19.6251C5.90971 19.7038 5.7129 19.7452 5.51374 19.7471C5.31457 19.7489 5.11703 19.711 4.93265 19.6357C4.74827 19.5603 4.58074 19.449 4.43984 19.3083C4.29894 19.1675 4.18748 19.0001 4.11197 18.8158C4.03646 18.6315 3.99841 18.434 4.00005 18.2348C4.00169 18.0356 4.04297 17.8388 4.1215 17.6557C4.20002 17.4727 4.31422 17.3071 4.45742 17.1687L9.76242 11.8637L4.45742 6.56068C4.17652 6.27943 4.01874 5.89818 4.01874 5.50068C4.01874 5.10318 4.17652 4.72093 4.45742 4.43968Z"
                  fill="black" />
              </svg>
            </button>
          </div>
          <div class="">
            @include('layouts.partials.sidebar-content')
          </div>
        </div>
      </div>

      <div
        class="flex-1 min-w-0 w-full flex flex-col rounded-lg inset-y-4 min-h-dvh md:pl-[260px] transition-[padding] duration-200"
        :class="notifOpen ? 'lg:pr-[25rem]' : 'lg:pr-0'">
        @include('layouts.partials.topbar')

        <main class="p-6 md:p-8">
          @yield('content')
        </main>
      </div>
      @include('layouts.partials.notification-panel')
    </div>
  @endif
  @stack('scripts')

</body>

</html>
