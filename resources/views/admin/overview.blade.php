@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <header class="mb-8 flex flex-col items-start justify-start gap-2">
    <h2 class="text-title lg:text-2xl font-semibold text-ink">Overview</h2>
    <p class="text-body-mid text-ink-muted font-semibold">Hello, {{ auth()->user()->name }}. Here's what's happening in
      Orbii.</p>
  </header>

  <main class="flex flex-col w-full items-start justify-start gap-4">
    <section class="grid grid-cols-1 w-full md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div
        class="w-full h-32 rounded-lg bg-canvas-soft border gap-3 border-hairline flex flex-col items-start justify-start p-4 hover:shadow-md transition-shadow duration-150">
        <h2 class="text-caption text-ink-muted">Total Users</h2>
        <div class="flex flex-col items-start justify-start">
          <span class="text-2xl font-semibold text-ink ml-4">{{ $userCount }}</span>
          <span class="text-body-mid text-ink-muted ml-4">+{{ $joinedUsers }} this week</span>
        </div>
      </div>
      <div
        class="w-full h-32 rounded-lg bg-canvas-soft border gap-3 border-hairline flex flex-col items-start justify-start p-4 hover:shadow-md transition-shadow duration-150">
        <h2 class="text-caption text-ink-muted">Total Clubs</h2>
        <div class="flex flex-col items-start justify-start">
          <span class="text-2xl font-semibold text-ink ml-4">{{ $clubCount }}</span>
        </div>
      </div>
      <div
        class="w-full h-32 rounded-lg bg-canvas-soft border gap-3 border-hairline flex flex-col items-start justify-start p-4 hover:shadow-md transition-shadow duration-150">
        <h2 class="text-caption text-ink-muted">pending Club Request</h2>
        <div class="flex flex-col items-start justify-start">
          <span class="text-2xl font-semibold text-ink ml-4">{{ $pendingClubCount }}</span>
          <span class="text-body-mid text-ink-muted ml-4">needs review</span>
        </div>
      </div>
      <div
        class="w-full h-32 rounded-lg bg-canvas-soft border gap-3 border-hairline flex flex-col items-start justify-start p-4 hover:shadow-md transition-shadow duration-150">
        <h2 class="text-caption text-ink-muted">Report</h2>
        <div class="flex flex-col items-start justify-start">
          <span class="text-2xl font-semibold text-ink ml-4">{{ $reports }}</span>
          <span class="text-body-mid text-ink-muted ml-4">needs review</span>
        </div>
      </div>
    </section>

    <section class="flex flex-col gap-4 lg:flex-row w-full">
      <section class="w-full lg:w-1/2 rounded-lg border border-hairline p-4 hover:shadow-md transition-shadow duration-150">
        <h1 class="text-2xl font-semibold text-ink">User Growth</h1>
        <div id="userChart" data-url="{{ route('api.chart') }}" class="w-full bg-canvas-soft lg:p-4"></div>
      </section>

      <section class="w-full lg:w-1/2 overflow-hidden rounded-lg border border-hairline relative p-4 flex flex-col items-end hover:shadow-md transition-shadow duration-150">
        <div class="hidden lg:block absolute -top-10 -right-10 w-64 h-64 rounded-full bg-primary/10 z-0"></div>
        <div class="hidden lg:block absolute -bottom-10 -left-10 w-72 h-72 rounded-full bg-primary/10 z-0"></div>
        <div class="flex flex-col gap-4 w-full mb-auto">
          <h1 class="text-2xl font-semibold text-ink w-full text-left">Account Status</h1>

          <div class="w-full p-4 flex flex-col gap-2">
            <p class="text-body-mid text-ink font-semibold">Active : <span
                class="text-ink-muted font-semibold tet-body-mid">{{ $user->where('status', 'active')->count() }}</span>
            </p>
            <p class="text-body-mid text-ink font-semibold">Suspended : <span
                class="text-ink-muted font-semibold tet-body-mid">{{ $user->where('status', 'suspended')->count() }}</span>
            </p>
            <p class="text-body-mid text-ink font-semibold">Banned : <span
                class="text-ink-muted font-semibold tet-body-mid">{{ $user->where('status', 'banned')->count() }}</span>
            </p>
          </div>
        </div>
        <a href="{{ route('admin.user-management') }}"
          class="text-blue-500 hover:underline hover:underline-offset-2 flex flex-row gap-2 items-center">Manage Users
          &RightArrow;
        </a>
      </section>
    </section>

    <section class="w-full overflow-hidden rounded-lg border border-hairline relative p-4 flex flex-col items-end hover:shadow-md transition-shadow duration-150">
      <h1 class="text-2xl w-full font-semibold text-ink">Recent Activity</h1>

      <div class="w-full my-8 gap-2 flex flex-col">
        @foreach($recentActivity as $activity)
          <div class="flex flex-row justify-between gap-2 lg:mx-8">
            <p class="text-body-mid text-ink"><span class="text-ink-muted">●</span> {{ $activity->user->name }}  {{ $activity->action }}</p>
            <p class="text-caption text-ink-muted">{{ $activity->created_at->diffForHumans() }}</p>
          </div>
        @endforeach
      </div>

      <a href="{{ route('admin.audit-logs') }}"
          class="text-blue-500 hover:underline hover:underline-offset-2 flex flex-row gap-2 items-center">View Audit Logs
          &RightArrow;
        </a>
    </section>
  </main>
@endsection
