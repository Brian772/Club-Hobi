@extends('layouts.app')

@section('title', 'Orbii | '. $profile->name)

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  @php
    $initial = strtoupper(substr($profile->name ?? 'U', 0, 1));
    $interests = $profile->interest_array;
  @endphp

  <main class="mx-auto max-w-5xl space-y-6 pb-8">
    <a href="{{ route('messages.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition hover:text-gray-950">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      Kembali ke pesan
    </a>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
      <div class="h-24 bg-[#173b3a] sm:h-32" style="background-image: repeating-linear-gradient(135deg, transparent, transparent 18px, rgba(255,255,255,.045) 18px, rgba(255,255,255,.045) 19px)"></div>
      <div class="px-5 pb-6 sm:px-8">
        <div class="-mt-12 flex flex-col gap-5 sm:-mt-14 sm:flex-row sm:items-end sm:justify-between">
          <div class="flex min-w-0 items-end gap-4">
            @if($profile->avatar_full_url)
              <img src="{{ $profile->avatar_full_url }}" alt="Foto profil {{ $profile->name }}" class="h-24 w-24 shrink-0 rounded-xl border-4 border-white object-cover shadow-sm sm:h-28 sm:w-28">
            @else
              <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-xl border-4 border-white bg-[#f0c36e] text-3xl font-bold text-[#173b3a] shadow-sm sm:h-28 sm:w-28" aria-label="Inisial {{ $profile->name }}">
                {{ $initial }}
              </div>
            @endif
            <div class="min-w-0 pb-1">
              <p class="mb-1 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Profil anggota</p>
              <h1 class="break-words text-2xl font-bold text-gray-950 sm:text-3xl">{{ $profile->name }}</h1>
              <p class="mt-1 inline-flex items-center gap-1.5 text-sm text-gray-600">
                <span class="h-2 w-2 rounded-full {{ $profile->isOnline() ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                {{ $profile->isOnline() ? 'Sedang online' : 'Offline' }}
              </p>
            </div>
          </div>

          @if ($user->id === $profile->id)
            <a href="{{ route('profile.edit') }}" class="inline-flex w-max items-center gap-2 rounded-md bg-[#173b3a] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#245351]">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z" />
              </svg>
              Edit profil
            </a>
          @else
            <a href="{{ $chatUrl }}" class="inline-flex w-max items-center gap-2 rounded-md bg-[#173b3a] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#245351]">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8A8.5 8.5 0 0 1 8.7 3.9a8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z" />
              </svg>
              {{ $chatUrl === route('messages.index') ? 'Kembali ke pesan' : 'Lanjutkan obrolan' }}
            </a>
          @endif
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4 border-t border-gray-100 pt-5 sm:max-w-md">
          <div>
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Bergabung sejak</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $profile->created_at?->locale('id')->translatedFormat('F Y') ?? 'Belum tersedia' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Komunitas</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $clubMemberships->count() }} klub</p>
          </div>
        </div>
      </div>
    </section>

    <div class="grid gap-8 md:grid-cols-[minmax(0,1fr)_minmax(240px,.75fr)] md:gap-12">
      <section class="space-y-7">
        <div>
          <h2 class="text-base font-bold text-gray-950">Tentang</h2>
          <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600">{{ filled($profile->bio) ? $profile->bio : 'Belum ada cerita yang dibagikan.' }}</p>
        </div>

        <div>
          <h2 class="text-base font-bold text-gray-950">Minat</h2>
          @if(count($interests))
            <ul class="mt-3 flex flex-wrap gap-2">
              @foreach($interests as $interest)
                <li class="rounded-full border border-[#d7e5df] bg-[#f2f7f3] px-3 py-1.5 text-sm font-medium text-[#285448]">{{ $interest }}</li>
              @endforeach
            </ul>
          @else
            <p class="mt-2 text-sm text-gray-500">Belum ada minat yang ditambahkan.</p>
          @endif
        </div>
      </section>

      <section class="border-t border-gray-200 pt-6 md:border-l md:border-t-0 md:pl-8 md:pt-0">
        <h2 class="text-base font-bold text-gray-950">Klub yang diikuti</h2>
        @if($clubMemberships->isNotEmpty())
          <ul class="mt-3 divide-y divide-gray-100">
            @foreach($clubMemberships as $membership)
              @if($membership->club)
                <li class="flex items-center justify-between gap-3 py-3 first:pt-0">
                  <span class="min-w-0 truncate text-sm font-medium text-gray-800">{{ $membership->club->name }}</span>
                  <span class="shrink-0 text-xs text-gray-500">Sejak {{ $membership->joined_at?->locale('id')->translatedFormat('M Y') ?? '—' }}</span>
                </li>
              @endif
            @endforeach
          </ul>
        @else
          <p class="mt-2 text-sm text-gray-500">Belum bergabung dengan klub.</p>
        @endif
      </section>
    </div>
  </main>
@endsection
