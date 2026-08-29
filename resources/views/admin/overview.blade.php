@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Overview</h1>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <div class="w-full h-32 rounded-lg bg-canvas-soft border border-hairline flex flex-col items-start justify-start p-4">
      <h2 class="text-caption text-ink-muted">Total Users:</h2>
      <span class="text-heading-1 font-bold text-ink ml-4 mt-2">{{ $userCount }}</span>
    </div>
    <div class="w-full h-32 rounded-lg bg-canvas-soft border border-hairline flex flex-col items-start justify-start p-4">
      <h2 class="text-caption text-ink-muted">Total Clubs:</h2>
      <span class="text-heading-1 font-bold text-ink ml-4 mt-2">{{ $clubCount }}</span>
    </div>
    <div class="w-full h-32 rounded-lg bg-canvas-soft border border-hairline flex flex-col items-start justify-start p-4">
      <h2 class="text-caption text-ink-muted">Active Members:</h2>
      <span class="text-heading-1 font-bold text-ink ml-4 mt-2">{{ $userActiveCount }}</span>
    </div>
  </div>

  <div class="w-full">
    <h1 class="text-2xl font-bold mb-4 mt-8">User Activity</h1>
    <div id="userChart" data-url="{{ route('api.chart') }}" class="w-full bg-canvas-soft rounded-md border border-hairline p-4"></div>
  </div>
@endsection
