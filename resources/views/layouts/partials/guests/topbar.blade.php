<header
  class="fixed top-4 z-30 flex items-center justify-between gap-4 p-2 inset-x-4 rounded-lg border border-hairline bg-canvas/40 backdrop-blur-md">
  <a href="{{ route('home') }}" class="px-4">
    <img src="{{ asset('images/orbii-v2.svg') }}" alt="Orbii Logo" alt="Orbii Logo" height="32"
      class="h-9 w-max object-contain">
  </a>

  <div class="hidden lg:flex flex-row gap-2 w-max">
    <a href="{{ route('home') }}#explore" class="w-max py-2 px-4 rounded-full hover:bg-canvas-muted">Explore</a>
    <a href="{{ route('home') }}#discover" class="w-max py-2 px-4 rounded-full hover:bg-canvas-muted">Discover</a>
    <a href="{{ route('home') }}#how-it-works" class="w-max py-2 px-4 rounded-full hover:bg-canvas-muted">How It Works</a>
    <a href="{{ route('home') }}#preview" class="w-max py-2 px-4 rounded-full hover:bg-canvas-muted">Preview</a>
  </div>

  <div class="hidden lg:flex flex-row gap-2 w-max">
    <a href="{{ route('login') }}" class="w-max py-2 px-4 text-ink hover:underline hover:underline-offset-2">Login</a>
    <a href="{{ route('register') }}"
      class="w-max px-4 py-2 rounded-full bg-primary text-white hover:bg-primary-active transition-colors duration-150">Register</a>
  </div>

  <div class="flex lg:hidden flex-row gap-4">
    <a href="{{ route('register') }}"
      class="flex lg:hidden px-4 py-2 rounded-full flex-row gap-2 items-center justify-center bg-primary font-semibold text-white hover:bg-primary-active transition-colors duration-150">Join
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        class="lucide lucide-arrow-right preview-icon">
        <path d="M5 12h14" />
        <path d="m12 5 7 7-7 7" />
      </svg>
    </a>
    <button @click="navOpen = true" class="text-ink hover:bg-canvas lg:hidden" aria-label="Buka menu">
      <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
  </div>
</header>
