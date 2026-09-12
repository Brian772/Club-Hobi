@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <section class="mx-auto">
    <header class="mb-4">
      <h1 class="text-2xl font-bold">Club Management</h1>
    </header>

    <main x-data="{ tab: 'all' }" class="border border-hairline p-4 rounded-lg">
      <div class="flex flex-row gap-2 border-b border-hairiline overflow-auto">
        <button @click="tab = 'all'"
          :class="tab === 'all' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted'"
          class="px-4 py-2 text-body font-semibold focus:outline-none">
          All Clubs
        </button>
        @foreach ($hobbies as $hobby)
          <button @click="tab = 'hobby-{{ $hobby->id }}'"
            :class="tab === 'hobby-{{ $hobby->id }}' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted'"
            class="px-4 py-2 text-body font-semibold focus:outline-none">
            {{ $hobby->name }}
          </button>
        @endforeach
      </div>

      <div x-show="tab === 'all'" class="flex flex-col mt-4 gap-4">
        @if ($clubs->isEmpty())
          <p class="text-sm text-ink-muted">No clubs available.</p>
        @endif
        @foreach ($clubs as $club)
          <div class="flex flex-row items-end lg:items-center justify-between border border-hairline p-2 rounded-lg">
            <div class="flex flex-col lg:flex-row gap-4">
              <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="{{ $club->name }}"
                width="128" height="64" class="rounded-xs">
              <div class="flex flex-col gap-1">
                <h2 class="text-lg font-semibold">{{ $club->name }} <span
              class="font-normal text-body-mid">· {{ $club->hobby->name }}</span></h2>
                <p class="text-sm text-ink-muted">{{ $club->description }}</p>
                <p class="text-caption text-ink font-semibold">Owner : <span
                    class="text-ink-muted font-normal">{{ $club->creator->name }}</span></p>
              </div>
            </div>
            <x-club-menu :club="$club" />
          </div>
        @endforeach
      </div>

      @foreach ($hobbies as $hobby)
        <div x-show="tab === 'hobby-{{ $hobby->id }}'" class="mt-4 gap-4">
          <?php $filteredClub = $clubs->where('hobby_id', $hobby->id); ?>
          @if ($filteredClub->isEmpty())
            <p class="text-sm text-ink-muted">No clubs available.</p>
          @endif
          @foreach ($filteredClub as $club)
            <div
              class="flex flex-row items-end lg:items-center justify-between border border-hairline p-2 rounded-lg">
              <div class="flex flex-col lg:flex-row gap-4">
                <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="{{ $club->name }}"
                  width="128" height="64" class="rounded-xs">
                <div class="flex flex-col gap-1">
                  <h2 class="text-lg font-semibold">{{ $club->name }} <span
              class="font-normal text-body-mid">· {{ $club->hobby->name }}</span></h2>
                  <p class="text-sm text-ink-muted">{{ $club->description }}</p>
                  <p class="text-caption text-ink font-semibold">Owner : <span
                      class="text-ink-muted font-normal">{{ $club->creator->name }}</span></p>
                </div>
              </div>
              <x-club-menu :club="$club" />
            </div>
          @endforeach
        </div>
      @endforeach
    </main>
  </section>
@endsection
