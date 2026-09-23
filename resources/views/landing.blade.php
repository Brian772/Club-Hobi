@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    * {
      scroll-behavior: smooth;
    }
  </style>
@endsection

@section('content')
  {{-- Hero Section --}}
  <section id="explore"
    class="flex mt-12 flex-col gap-8 items-center md:items-start justify-center min-h-dvh max-w-7xl mx-auto bg-canvas-soft">
    <div class="flex flex-row items-center justify-center gap-10 w-full">
      <div class="flex flex-col w-full md:w-1/2">
        <div
          class="text-center flex flex-col gap-2 justify-center items-center md:text-left md:items-start md:justify-start md:gap-4">
          <h1 class="text-heading-1 max-w-100 md:text-display-2 xl:text-display-1 text-ink font-bold">Find Your
            <span class="md:relative md:inline-block text-primary">
              People.
              <svg
                class="pointer-events-none hidden md:inline-block absolute left-0 -bottom-3 h-5 overflow-visible w-full text-accent-yellow"
                viewBox="0 0 200 12" preserveAspectRatio="none" fill="none" aria-hidden="true">
                <path d="M 0 10 C 70 3 140 3 200 10" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                  vector-effect="non-scaling-stroke" />
              </svg>
            </span>
          </h1>
          <p class="text-ink-secondary md:mt-3 max-w-150 text-body-mid">Discover hobbies, join clubs, and connect with
            people who
            are into the exact same things. Moderated, inspiring, and
            100% teen-led.</p>
        </div>
        <div class="flex flex-col gap-2 mt-4 w-full md:w-max">
          <a href="{{ route('register') }}"
            class="group flex flex-row gap-2 w-full md:w-max px-4 py-2 items-center justify-center rounded-full bg-primary text-white font-semibold">
            Join Orbii
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-arrow-right preview-icon group-hover:translate-x-1 transition-transform duration-300">
              <path d="M5 12h14" />
              <path d="m12 5 7 7-7 7" />
            </svg></a>
        </div>
        @php
          $avatars = [asset('images/avatars/1.jpg'), asset('images/avatars/2.jpg'), asset('images/avatars/3.jpg')];
        @endphp

        <div
          class="hidden md:inline-flex mt-12 items-center w-max justify-center gap-4 rounded-full bg-canvas border border-hairline px-5 py-3">
          <div class="flex -space-x-4">
            @foreach ($avatars as $avatar)
              <img class="size-11 rounded-full border-2 border-white object-cover" src="{{ $avatar }}"
                alt="Avatar" draggable="false">
            @endforeach
          </div>
          <div clas="flex flex-col gap-0.5">
            <div class="flex items-center gap-2">
              <div class="flex text-accent-yellow">
                @for ($i = 0; $i < 5; $i++)
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#ffcc00"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-star preview-icon">
                    <path
                      d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z" />
                  </svg>
                @endfor
              </div>
              <span class="text-body-mid font-semibold text-ink-secondary">4.9</span>
            </div>
            <p class="text-caption text-ink-muted">14.200+ active members</p>
          </div>
        </div>
      </div>
      <div class="hidden md:flex md:w-1/2 items-center justify-center">
        <img src="{{ asset('images/cover/hero-img.png') }}" alt="Hero Image" draggable="false"
          class="w-max h-auto max-h-150 object-cover">
      </div>
    </div>

    @php
      $avatars = [asset('images/avatars/1.jpg'), asset('images/avatars/2.jpg'), asset('images/avatars/3.jpg')];
    @endphp

    <div
      class="inline-flex md:hidden items-center justify-center gap-4 rounded-full bg-canvas border border-hairline px-5 py-3 mt-8">
      <div class="flex -space-x-4">
        @foreach ($avatars as $avatar)
          <img class="size-11 rounded-full border-2 border-white object-cover" src="{{ $avatar }}" alt="Avatar"
            draggable="false">
        @endforeach
      </div>
      <div clas="flex flex-col gap-0.5">
        <div class="flex items-center gap-2">
          <div class="flex text-accent-yellow">
            @for ($i = 0; $i < 5; $i++)
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#ffcc00"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-star preview-icon">
                <path
                  d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z" />
              </svg>
            @endfor
          </div>
          <span class="text-body-mid font-semibold text-ink-secondary">4.9</span>
        </div>
        <p class="text-caption text-ink-muted">14.200+ active members</p>
      </div>
    </div>

    <div class="flex flex-col w-full -space-y-40 md:hidden">
      <div
        class="flex flex-col gap-4 bg-canvas border border-hairline rounded-xl p-4 mt-8 w-full rotate-2 hover:z-20 hover:-translate-y-2 hover:rotate-0 transition-transform duration-300">
        <header class="flex flex-row items-center justify-between">
          <span class="uppercase px-2 py-1 rounded-full text-overline text-primary bg-primary/10">Coding &
            Robotics</span>
          <span class="px-2 py-1 text-overline text-ink-muted">Build & Ship Teens</span>
        </header>
        <div class="flex flex-row gap-4 w-full">
          <img src="{{ asset('images/cover/2.png') }}" alt="cover" class="size-24 rounded-lg object-cover">
          <div class="flex flex-col gap-2">
            <div class="flex flex-col gap-0.5 mb-auto">
              <h2 class="text-body-mid text-ink font-semibold">Coding & Robotics</h2>
              <p class="text-caption text-ink-secondary">Build and ship amazing projects with...</p>
            </div>
            <div class="flex flex-row gap-4 items-center">
              <div class="flex flex-row gap-2 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-users preview-icon">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                  <path d="M16 3.128a4 4 0 0 1 0 7.744" />
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                  <circle cx="9" cy="7" r="4" />
                </svg>
                <span class="text-caption text-ink-muted">2.420</span>
              </div>
              <div class="flex flex-row gap-2 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" class="lucide lucide-messages-square preview-icon">
                  <path
                    d="M16 10a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 14.286V4a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                  <path
                    d="M20 9a2 2 0 0 1 2 2v10.286a.71.71 0 0 1-1.212.502l-2.202-2.202A2 2 0 0 0 17.172 19H10a2 2 0 0 1-2-2v-1" />
                </svg>
                <span class="text-caption text-ink-muted">73 post</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="z-10 flex flex-col gap-4 bg-canvas border border-hairline rounded-xl p-4 mt-8 w-full hover:translate-y-3 transition-transform duration-300">
        <header class="flex flex-row items-center justify-between">
          <span class="uppercase px-2 py-1 rounded-full text-overline text-primary bg-primary/10">Photography</span>
          <span class="px-2 py-1 text-overline text-ink-muted">35mm & Digital</span>
        </header>
        <div class="flex flex-row gap-4 w-full">
          <img src="{{ asset('images/cover/1.png') }}" alt="cover" class="size-24 rounded-lg object-cover">
          <div class="flex flex-col gap-2">
            <div class="flex flex-col gap-0.5 mb-auto">
              <h2 class="text-body-mid text-ink font-semibold">Street Shooters Club</h2>
              <p class="text-caption text-ink-secondary">Golden hour challange entire open...</p>
            </div>
            <div class="flex flex-row gap-4 items-center">
              <div class="flex flex-row gap-2 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" class="lucide lucide-users preview-icon">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                  <path d="M16 3.128a4 4 0 0 1 0 7.744" />
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                  <circle cx="9" cy="7" r="4" />
                </svg>
                <span class="text-caption text-ink-muted">1.280</span>
              </div>
              <div class="flex flex-row gap-2 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" class="lucide lucide-messages-square preview-icon">
                  <path
                    d="M16 10a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 14.286V4a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                  <path
                    d="M20 9a2 2 0 0 1 2 2v10.286a.71.71 0 0 1-1.212.502l-2.202-2.202A2 2 0 0 0 17.172 19H10a2 2 0 0 1-2-2v-1" />
                </svg>
                <span class="text-caption text-ink-muted">32 post</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Discover --}}
  <section id="discover"
    class="flex flex-col w-full gap-8 pt-24 items-center justify-center mx-auto max-w-7xl bg-canvas-soft">
    <div class="flex flex-col gap-0.5 items-start justify-start w-full">
      <p class="text-caption text-primary uppercase font-semibold">Discover By Passion</p>
      <h2 class="text-title md:text-3xl text-ink font-semibold md:font-bold">Whatever you're into, there's a space for
        you.</h2>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 w-full">
      {{-- Photography --}}
      <div
        class="flex flex-col gap-4 rounded-3xl p-4 bg-canvas border border-hairline w-full h-full hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
        <div class="p-3 rounded-md bg-primary/10 text-primary w-max h-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-camera preview-icon">
            <path
              d="M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.759-1.048l.489-.904A2 2 0 0 1 10.004 4z" />
            <circle cx="12" cy="13" r="3" />
          </svg>
        </div>
        <div class="flex flex-col gap-0.5 xl:gap-2">
          <div class="flex flex-col gap-0.5 xl:flex-row xl:justify-between xl:items-center">
            <h3 class="text-body-mid xl:text-title text-ink">Photography</h3>
            <p
              class="text-caption text-ink-muted xl:bg-canvas-faint xl:px-2 xl:py-1 xl:w-max xl:rounded-full xl:text-ink">
              340+ Clubs</p>
          </div>
          <p class="hidden xl:inline text-body-mid text-ink">Street, 35mm film, portraits,
            darkroom & Lightroom mobile
            editing tips.</p>
        </div>
      </div>
      {{-- Gaming --}}
      <div
        class="flex flex-col gap-4 rounded-3xl p-4 bg-canvas border border-hairline w-full h-full hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
        <div class="p-3 rounded-md bg-purple-500/10 text-purple-500 w-max h-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-gamepad-2 preview-icon">
            <line x1="6" x2="10" y1="11" y2="11" />
            <line x1="8" x2="8" y1="9" y2="13" />
            <line x1="15" x2="15.01" y1="12" y2="12" />
            <line x1="18" x2="18.01" y1="10" y2="10" />
            <path
              d="M17.32 5H6.68a4 4 0 0 0-3.978 3.59c-.006.052-.01.101-.017.152C2.604 9.416 2 14.456 2 16a3 3 0 0 0 3 3c1 0 1.5-.5 2-1l1.414-1.414A2 2 0 0 1 9.828 16h4.344a2 2 0 0 1 1.414.586L17 18c.5.5 1 1 2 1a3 3 0 0 0 3-3c0-1.545-.604-6.584-.685-7.258-.007-.05-.011-.1-.017-.151A4 4 0 0 0 17.32 5z" />
          </svg>
        </div>
        <div class="flex flex-col gap-0.5 xl:gap-2">
          <div class="flex flex-col gap-0.5 xl:flex-row xl:justify-between xl:items-center">
            <h3 class="text-body-mid xl:text-title text-ink">Gaming</h3>
            <p
              class="text-caption text-ink-muted xl:bg-canvas-faint xl:px-2 xl:py-1 xl:w-max xl:rounded-full xl:text-ink">
              520+ Clubs</p>
          </div>
          <p class="hidden xl:inline text-body-mid text-ink">Casual lobbies, Minecraft smp,
            speedrunning, Valorant teams &
            indie game dev.</p>
        </div>
      </div>
      {{-- Music --}}
      <div
        class="flex flex-col gap-4 rounded-3xl p-4 bg-canvas border border-hairline w-full h-full hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
        <div class="p-3 rounded-md bg-pink-500/10 text-pink-500 w-max h-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-headphones preview-icon">
            <path
              d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3" />
          </svg>
        </div>
        <div class="flex flex-col gap-0.5 xl:gap-2">
          <div class="flex flex-col gap-0.5 xl:flex-row xl:justify-between xl:items-center">
            <h3 class="text-body-mid xl:text-title text-ink">Music</h3>
            <p
              class="text-caption text-ink-muted xl:bg-canvas-faint xl:px-2 xl:py-1 xl:w-max xl:rounded-full xl:text-ink">
              290+ Clubs</p>
          </div>
          <p class="hidden xl:inline text-body-mid text-ink">Garage bands, guitar tabs, FL
            Studio beats, songwriting
            feedback & synth jams.</p>
        </div>
      </div>
      {{-- Art & Desaign --}}
      <div
        class="flex flex-col gap-4 rounded-3xl p-4 bg-canvas border border-hairline w-full h-full hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
        <div class="p-3 rounded-md bg-orange-500/10 text-orange-500 w-max h-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-brush preview-icon">
            <path d="m11 10 3 3" />
            <path d="M6.5 21A3.5 3.5 0 1 0 3 17.5a2.62 2.62 0 0 1-.708 1.792A1 1 0 0 0 3 21z" />
            <path d="M9.969 17.031 21.378 5.624a1 1 0 0 0-3.002-3.002L6.967 14.031" />
          </svg>
        </div>
        <div class="flex flex-col gap-0.5 xl:gap-2">
          <div class="flex flex-col gap-0.5 xl:flex-row xl:justify-between xl:items-center">
            <h3 class="text-body-mid xl:text-title text-ink">Art & Design</h3>
            <p
              class="text-caption text-ink-muted xl:bg-canvas-faint xl:px-2 xl:py-1 xl:w-max xl:rounded-full xl:text-ink">
              410+ Clubs</p>
          </div>
          <p class="hidden xl:inline text-body-mid text-ink">Digital illustration, Procreate
            sketches, Blender 3D, typography
            & anime art.</p>
        </div>
      </div>
      {{-- Coding --}}
      <div
        class="flex flex-col gap-4 rounded-3xl p-4 bg-canvas border border-hairline w-full h-full hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
        <div class="p-3 rounded-md bg-emerald-500/10 text-emerald-500 w-max h-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-braces preview-icon">
            <path d="M8 3H7a2 2 0 0 0-2 2v5a2 2 0 0 1-2 2 2 2 0 0 1 2 2v5c0 1.1.9 2 2 2h1" />
            <path d="M16 21h1a2 2 0 0 0 2-2v-5c0-1.1.9-2 2-2a2 2 0 0 1-2-2V5a2 2 0 0 0-2-2h-1" />
          </svg>
        </div>
        <div class="flex flex-col gap-0.5 xl:gap-2">
          <div class="flex flex-col gap-0.5 xl:flex-row xl:justify-between xl:items-center">
            <h3 class="text-body-mid xl:text-title text-ink">Coding</h3>
            <p
              class="text-caption text-ink-muted xl:bg-canvas-faint xl:px-2 xl:py-1 xl:w-max xl:rounded-full xl:text-ink">
              290+ Clubs</p>
          </div>
          <p class="hidden xl:inline text-body-mid text-ink">Web development, Python
            scripts, robotics, mobile iOS apps
            & weekend hackathons.</p>
        </div>
      </div>
      {{-- Sport & Action --}}
      <div
        class="flex flex-col gap-4 rounded-3xl p-4 bg-canvas border border-hairline w-full h-full hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
        <div class="p-3 rounded-md bg-primary/10 text-primary w-max h-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-sport-shoe preview-icon">
            <path d="m15 10.42 4.8-5.07" />
            <path d="M19 18h3" />
            <path
              d="M9.5 22 21.414 9.415A2 2 0 0 0 21.2 6.4l-5.61-4.208A1 1 0 0 0 14 3v2a2 2 0 0 1-1.394 1.906L8.677 8.053A1 1 0 0 0 8 9c-.155 6.393-2.082 9-4 9a2 2 0 0 0 0 4h14" />
          </svg>
        </div>
        <div class="flex flex-col gap-0.5 xl:gap-2">
          <div class="flex flex-col gap-0.5 xl:flex-row xl:justify-between xl:items-center">
            <h3 class="text-body-mid xl:text-title text-ink">Sport & Action</h3>
            <p
              class="text-caption text-ink-muted xl:bg-canvas-faint xl:px-2 xl:py-1 xl:w-max xl:rounded-full xl:text-ink">
              225+ Clubs</p>
          </div>
          <p class="hidden xl:inline text-body-mid text-ink">Skateboarding spots, street
            hoops, track & field, bouldering,
            and surf crews.</p>
        </div>
      </div>
      {{-- Outdoors --}}
      <div
        class="flex flex-col gap-4 rounded-3xl p-4 bg-canvas border border-hairline w-full h-full hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
        <div class="p-3 rounded-md bg-green-500/10 text-green-500 w-max h-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-trees preview-icon">
            <path d="M10 10v.2A3 3 0 0 1 8.9 16H5a3 3 0 0 1-1-5.8V10a3 3 0 0 1 6 0Z" />
            <path d="M7 16v6" />
            <path d="M13 19v3" />
            <path d="M12 19h8.3a1 1 0 0 0 .7-1.7L18 14h.3a1 1 0 0 0 .7-1.7L16 9h.2a1 1 0 0 0 .8-1.7L13 3l-1.4 1.5" />
          </svg>
        </div>
        <div class="flex flex-col gap-0.5 xl:gap-2">
          <div class="flex flex-col gap-0.5 xl:flex-row xl:justify-between xl:items-center">
            <h3 class="text-body-mid xl:text-title text-ink">Outdoors</h3>
            <p
              class="text-caption text-ink-muted xl:bg-canvas-faint xl:px-2 xl:py-1 xl:w-max xl:rounded-full xl:text-ink">
              180+ Clubs</p>
          </div>
          <p class="hidden xl:inline text-body-mid text-ink">Trail exploration, birding logs,
            national park camps & outdoor
            gear swaps.</p>
        </div>
      </div>
      {{-- Writing & Lore --}}
      <div
        class="flex flex-col gap-4 rounded-3xl p-4 bg-canvas border border-hairline w-full h-full hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
        <div class="p-3 rounded-md bg-pink-500/10 text-pink-500 w-max h-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-book-open-text preview-icon">
            <path d="M12 5v16" />
            <path d="M16 13h2" />
            <path d="M16 9h2" />
            <path
              d="M20.001 19A2 2 0 0022 17V5a2 2 0 00-1.999-2L16 3.002A5 5 0 0012 5a5 5 0 00-4-2H4a2 2 0 00-2 2v12a2 2 0 001.999 2H8a5 5 0 014 2 5 5 0 014-2z" />
            <path d="M6 13h2" />
            <path d="M6 9h2" />
          </svg>
        </div>
        <div class="flex flex-col gap-0.5 xl:gap-2">
          <div class="flex flex-col gap-0.5 xl:flex-row xl:justify-between xl:items-center">
            <h3 class="text-body-mid xl:text-title text-ink">Writing & Lore</h3>
            <p
              class="text-caption text-ink-muted xl:bg-canvas-faint xl:px-2 xl:py-1 xl:w-max xl:rounded-full xl:text-caption xl:text-ink">
              290+ Clubs</p>
          </div>
          <p class="hidden xl:inline text-body-mid text-ink">Fantasy book circles, creative
            flash fiction, slam poetry &
            screenwriting swaps.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- How it works --}}
  <section id="how-it-works"
    class="flex flex-col w-full gap-8 items-center pt-42 justify-center mx-auto max-w-7xl bg-canvas-soft">
    <div class="flex flex-col gap-0.5 items-start justify-start w-full">
      <p class="text-caption text-primary uppercase font-semibold">How It Works</p>
      <h2 class="text-title md:text-3xl text-ink font-semibold md:font-bold">From hobby to community in 3 steps.</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
      {{-- Step 1 --}}
      <div
        class="flex flex-row xl:flex-col gap-4 rounded-3xl p-4 bg-canvas border border-hairline w-full h-max hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
        <span class="p-4 xl:hidden rounded-full bg-primary/10 text-primary w-max h-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-search preview-icon">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" x2="16.65" y1="21" y2="16.65" />
          </svg>
        </span>
        <div class="hidden xl:flex flex-row justify-between items-center">
          <span class="text-display-2 text-primary font-bold">01</span>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-search preview-icon text-ink-muted">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" x2="16.65" y1="21" y2="16.65" />
          </svg>
        </div>
        <div class="flex flex-col gap-2">
          <h3 class="text-title text-ink">Find what sparks you</h3>
          <p class="text-body-mid text-ink-muted">Search or browse hundreds of hobbies and find active communities near
            you or around the world.</p>
          <div class="flex flex-row gap-2 items-center xl:hidden">
            <span class="rounded-full px-2 py-1 text-caption bg-canvas-faint text-ink">Photography</span>
            <span class="rounded-full px-2 py-1 text-caption bg-canvas-faint text-ink">Gaming</span>
            <span class="rounded-full px-2 py-1 text-caption bg-canvas-faint text-ink">Hiking</span>
          </div>
          <div
            class="hidden xl:flex flex-col gap-4 items-start bg-canvas-light border border-hairline w-full rounded-lg p-4">
            <div class="flex flex-row items-center bg-canvas p-2 rounded-md justify-between w-full">
              <div class="flex flex-row gap-2 w-max items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" class="lucide lucide-search preview-icon">
                  <circle cx="11" cy="11" r="8" />
                  <line x1="21" x2="16.65" y1="21" y2="16.65" />
                </svg>
                <span class="text-caption text-ink-muted">search your hobby</span>
              </div>
              <span class="text-caption text-primary bg-primary/10 px-2 py-1 rounded">Enter</span>
            </div>
            <div class="flex flex-row gap-2 items-center">
              <span class="rounded-full px-2 py-1 text-caption bg-canvas-faint text-ink">Photography</span>
              <span class="rounded-full px-2 py-1 text-caption bg-canvas-faint text-ink">Gaming</span>
              <span class="rounded-full px-2 py-1 text-caption bg-canvas-faint text-ink">Hiking</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Step 2 --}}
      <div
        class="flex flex-row xl:flex-col gap-4 rounded-3xl p-4 bg-canvas border border-hairline w-full h-max hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
        <span class="p-4 xl:hidden rounded-full bg-primary/10 text-primary w-max h-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-user-plus preview-icon">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <line x1="19" x2="19" y1="8" y2="14" />
            <line x1="22" x2="16" y1="11" y2="11" />
          </svg>
        </span>
        <div class="hidden xl:flex flex-row justify-between items-center">
          <span class="text-display-2 text-primary font-bold">02</span>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-user-plus preview-icon">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <line x1="19" x2="19" y1="8" y2="14" />
            <line x1="22" x2="16" y1="11" y2="11" />
          </svg>
        </div>
        <div class="flex flex-col gap-2">
          <h3 class="text-title text-ink">Join your people</h3>
          <p class="text-body-mid text-ink-muted">Apply with one click or join open clubs
            moderated for teen safety with verified peer
            leaders.</p>
          <div class="flex flex-col gap-4 p-2 xl:p-4 bg-canvas-light border border-hairline w-full rounded-lg">
            <div class="flex flex-row gap-2 items-center">
              <span class="size-11 rounded-full bg-primary/10 flex items-center justify-center">
                <span class="text-body-mid text-primary font-semibold">BC</span>
              </span>
              <div class="flex flex-col gap-0.5">
                <span class="text-body-mid font-semibold text-ink">Bookworm Collective</span>
              </div>
            </div>
            <div class="hidden xl:flex w-full px-4 py-2 bg-primary rounded-lg">
              <span class=" w-full text-body-mid text-white font-semibold text-center">Join Club</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Step 3 --}}
      <div
        class="flex flex-row xl:flex-col gap-4 rounded-3xl p-4 bg-canvas border border-hairline w-full h-max hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
        <span class="p-4 xl:hidden rounded-full bg-primary/10 text-primary w-max h-max">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-messages-square preview-icon">
            <path
              d="M16 10a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 14.286V4a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
            <path
              d="M20 9a2 2 0 0 1 2 2v10.286a.71.71 0 0 1-1.212.502l-2.202-2.202A2 2 0 0 0 17.172 19H10a2 2 0 0 1-2-2v-1" />
          </svg>
        </span>
        <div class="hidden xl:flex flex-row justify-between items-center">
          <span class="text-display-2 text-primary font-bold">03</span>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-messages-square preview-icon">
            <path
              d="M16 10a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 14.286V4a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
            <path
              d="M20 9a2 2 0 0 1 2 2v10.286a.71.71 0 0 1-1.212.502l-2.202-2.202A2 2 0 0 0 17.172 19H10a2 2 0 0 1-2-2v-1" />
          </svg>
        </div>
        <div class="flex flex-col gap-2">
          <h3 class="text-title text-ink">Share & connect</h3>
          <p class="text-body-mid text-ink-muted">Share your progress, post photos, join voice
            hangouts, and collaborate on cool weekend
            projects.</p>
          <div class="flex flex-col gap-2 rounded-lg p-2 xl:p-4 bg-canvas-light border border-hairline w-full">
            <div class="flex flex-row items-center justify-between">
              <p class="text-body-mid text-ink font-semibold">Project WIP upploaded</p>
              <span class="text-primary font-semibold text-body-mid inline xl:hidden">+4 replies</span>
              <span class="text-ink-muted text-body-mid hidden xl:inline">just now</span>
            </div>
            <div class="hidden xl:flex flex-row gap-4 items-center">
              <span class="flex flex-row items-center gap-2 text-accent-red px-2 py-1 bg-canvas rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                  fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" class="lucide lucide-heart preview-icon">
                  <path
                    d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5" />
                </svg>
                <span class="text-ink text-body-mid">235</span>
              </span>
              <span class="flex flex-row items-center gap-2 px-2 py-1 text-ink bg-canvas rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" class="lucide lucide-message-square preview-icon">
                  <path
                    d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z" />
                </svg>
                <span class="text-ink text-body-mid">65</span>
              </span>
              <span class="flex flex-row items-center gap-2 px-2 py-1 text-primary bg-primary/10 rounded-full">
                + Reply
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- preview --}}
  <section id="preview"
    class="flex flex-col w-full gap-8 items-center pt-42 justify-center mx-auto max-w-7xl bg-canvas-soft">
    <div class="flex flex-col gap-0.5 items-start justify-start w-full">
      <p class="text-caption text-primary uppercase font-semibold">Interface Preview</p>
      <h2 class="text-title md:text-3xl text-ink font-semibold md:font-bold">Not just a club. A community.</h2>
    </div>

    <div
      class="w-full flex flex-col gap-4 rounded-xl p-2 xl:p-4 bg-canvas border border-hairline hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
      <div class="flex flex-row items-center justify-between">
        <div class="flex flex-row gap-4 items-center">
          <span class="p-4 text-primary bg-primary/10 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-camera preview-icon">
              <path
                d="M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.759-1.048l.489-.904A2 2 0 0 1 10.004 4z" />
              <circle cx="12" cy="13" r="3" />
            </svg>
          </span>
          <div class="flex flex-col gap-0.5">
            <h3 class="text-body-mid xl:text-title text-ink font-semibold">Golden Hour Collective</h3>
            <p class="text-ink-muted text-caption">3.840 members</p>
          </div>
        </div>
        <span class="px-4 py-2 rounded-full bg-primary text-white">
          Joined
        </span>
      </div>
      <div class="flex flex-row gap-4 items-center border-b border-hairline w-full pb-2">
        <span class="px-4 py-2 text-primary font-semibold underline underline-offset-2 rounded-full">Feed</span>
      </div>
      <div class="flex flex-col gap-4 mt-8 w-full mx-auto max-w-150">
        <div class="flex flex-row items-center w-full justify-between">
          <div class="flex flex-row gap-2 items-center">
            <img src="{{ asset('images/avatars/4.jpg') }}" alt="" draggable="false"
              class="size-10 rounded-full object-cover">
            <div class="flex flex-col gap-0.5">
              <span class="text-body-mid text-ink font-semibold">Eleanor Pena</span>
              <span class="text-caption text-ink-muted">2 hours ago • Photography</span>
            </div>
          </div>
          <span class="text-ink-muted text-caption">•••</span>
        </div>
        <p class="text-ink text-body-mid">Caught this reflection after yesterday's rainstorm near the old harbor docks.
          Any
          thoughts on the warm amber color grading, or does it feel too saturated?</p>
        <img src="{{ asset('images/cover/3.jpg') }}" alt="" draggable="false"
          class="rounded-xl object-cover w-max max-h-75">
        <div class="flex flex-row gap-4 items-center">
          <span class="flex flex-row items-center gap-2 text-accent-red px-2 py-1 bg-canvas rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
              fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round" class="lucide lucide-heart preview-icon">
              <path
                d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5" />
            </svg>
            <span class="text-ink text-body-mid">235</span>
          </span>
          <span class="flex flex-row items-center gap-2 px-2 py-1 text-ink bg-canvas rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-message-square preview-icon">
              <path
                d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z" />
            </svg>
            <span class="text-ink text-body-mid">65</span>
          </span>
        </div>
      </div>
    </div>
  </section>

  {{-- CTA --}}
  <section class="flex flex-col w-full items-center pt-42 justify-center mx-auto max-w-7xl bg-canvas-soft">
    <div class="flex relative overflow-hidden flex-col gap-4 items-center justify-center bg-[#1E4BE0] rounded-3xl w-full hover:-translate-y-1 hover:shadow-md transition-transform duration-300">
      <div class="flex flex-col z-10 gap-4 p-8 py-12 items-center w-full justify-center max-w-200">
        <span class="text-ink text-overline px-2 py-1 bg-white rounded-full w-max mb-4">Join our community</span>
        <h2 class="text-heading-2 xl:text-display-1 text-white font-semibold text-center mb-4">Your hobby. Your people.
          Your space</h2>
        <p class="text-body-mid text-white text-center font-semibold mb-4">Find a community that actually gets what you’re passionate about. Free to join, built for teens (13–19), always safe.</p>
        <a href="{{ route('register') }}"
          class="group px-4 py-2 rounded-full bg-white flex flex-row gap-2 items-center text-primary font-semibold text-body-mid w-max mt-4">Join
          Orbii Now
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-arrow-right preview-icon group-hover:translate-x-1 transition-transform duration-300">
            <path d="M5 12h14" />
            <path d="m12 5 7 7-7 7" />
          </svg>
        </a>
        <div class="flex flex-col lg:flex-row gap-3 lg:gap-6 items-center justify-center w-full mt-4">
          <span class="text-white text-body-mid">Takes less than 60 seconds</span>
          <span class="text-white text-body-mid">100% Free • No credit card</span>
          <span class="text-white text-body-mid">Strictly Teen Moderated (Ages 13–19)</span>
        </div>
      </div>
      <div class="absolute -top-10 -left-10 lg:-top-20 lg:-left-20 size-50 lg:size-100 bg-primary-active/40 rounded-full"></div>
      <div class="absolute -bottom-10 -right-10 lg:-bottom-20 lg:-right-20 size-50 lg:size-100 bg-primary-active/40 rounded-full"></div>
    </div>
  </section>
@endsection
