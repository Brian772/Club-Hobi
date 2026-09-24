@extends('layouts.app')

@section('title', 'Orbii | Overview')

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
      <section
        class="w-full lg:w-1/2 rounded-lg border border-hairline p-4 hover:shadow-md transition-shadow duration-150">
        <h1 class="text-2xl font-semibold text-ink">User Growth</h1>
        <div id="userChart" data-url="{{ route('api.chart') }}" class="w-full bg-canvas-soft lg:p-4"></div>
      </section>

      <section
        class="w-full lg:w-1/2 overflow-hidden rounded-lg border border-hairline relative p-4 flex flex-col items-end hover:shadow-md transition-shadow duration-150">
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

    <section x-data="{
        storeModal: false,
        hobbyName: @js(old('name', '')),
        deleteModal: false,
        deleteName: '',
        deleteUrl: '',
    }" class="flex flex-col lg:flex-row gap-4 w-full lg:h-72">
      {{-- Add New Hobby --}}
      <section
        class="w-full lg:w-1/2 2xl:w-full h-full rounded-lg border border-hairline p-4 flex flex-col hover:shadow-md transition-shadow duration-150">
        <h1 class="text-title lg:text-2xl text-ink font-semibold">Add New Hobbies</h1>

        <form x-ref="storeForm" action="{{ route('admin.hobbies.store') }}" method="POST"
          @submit.prevent="storeModal = true" class="mt-4 flex flex-1 flex-col gap-4">
          @csrf
          @method('PUT')
          <div class="flex flex-col gap-2 h-full mb-4 lg:mb-auto mt-8">
            <label for="name">Hobby Name</label>
            <input type="text" name="name" x-model="hobbyName" required placeholder="Enter New Hobby Name"
              class="w-full rounded-lg border border-hairline p-2 text-body-mid text-ink focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            @error('name')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
          <button type="submit"
            class="w-max bg-primary/10 text-primary rounded-md px-4 py-2 hover:bg-primary hover:text-white transition-colors duration-150">
            Add Hobby
          </button>
        </form>
      </section>

      {{-- Hobbies List --}}
      <section
        class="w-full lg:w-1/2 2xl:w-1/3 h-73 lg:h-full rounded-lg border border-hairline p-4 flex flex-col hover:shadow-md transition-shadow duration-150 overflow-hidden">
        <h1 class="text-title lg:text-2xl text-ink font-semibold">Hobbies List</h1>

        <div class="mt-4 h-full overflow-x-auto">
          <div class="flex flex-col gap-2 lg:mr-2">
            @foreach ($hobbies as $hobby)
              <div
                class="group w-full rounded-md border border-canvas hover:border-hairline p-2 flex flex-row justify-between items-center">
                <p class="text-body-mid text-ink">{{ $hobby->name }}</p>
                <div class="flex flex-row gap-2">
                  <button type="button"
                    @click="deleteUrl = @js(route('admin.hobbies.delete', $hobby->id)); deleteName = @js($hobby->name); deleteModal = true;"
                    class="rounded p-2 text-red-500 opacity-100 transition-opacity duration-150 hover:bg-red-500/10 focus-visible:opacity-100 lg:opacity-0 lg:group-hover:opacity-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                      fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-trash preview-icon">
                      <path d="M10 11v6" />
                      <path d="M14 11v6" />
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                      <path d="M3 6h18" />
                      <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                    </svg>
                  </button>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </section>

      {{-- Add Hobby Modal --}}
      <div x-cloak x-show="storeModal" x-transition.opacity @keydown.escape.window="storeModal = false"
        @click.self="storeModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div role="dialog" aria-modal="true" @click.away="storeModal = false"
          class="bg-white rounded-lg p-6 w-sm lg:w-md flex flex-col items-start jusfity-end gap-6">
          <div class="flex flex-col w-full jusfitu-start items-start">
            <h2 class="text-title font-semibold text-ink">Add this hobby?</h2>
            <p class="text-body-mid text-ink-muted mt-6">Are you sure you want to add the hobby "<span
                class="font-semibold text-ink" x-text="hobbyName"></span>"?</p>
          </div>
          <div class="flex flex-row gap-2 w-full justify-end items-center">
            <button @click="$refs.storeForm.submit()"
              class="px-4 py-2 bg-primary/10 text-primary hover:text-white rounded hover:bg-primary transition-colors duration-150">Yes, Add a hobby</button>
            <button @click="storeModal = false"
              class="px-4 py-2 bg-gray-200 text-ink rounded hover:bg-gray-300 transition-colors duration-150">Cancel</button>
          </div>
        </div>
      </div>

      {{-- Delete Hobby Modal --}}
      <div x-cloak x-show="deleteModal" x-transition.opacity @keydown.escape.window="deleteModal = false"
        @click.self="deleteModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div role="dialog" aria-modal="true" @click.away="deleteModal = false"
          class="bg-white rounded-lg p-6 w-sm lg:w-md flex flex-col items-start justify-end gap-6">
          <div class="flex flex-col w-full justify-start items-start">
            <h2 class="text-title font-semibold text-ink">Delete this hobby?</h2>
            <p class="text-body-mid text-ink-muted mt-6">The hobby "<span class="font-semibold text-ink" x-text="deleteName"></span>"
              will be permanently deleted.</p>
          </div>
          <div class="flex flex-row gap-2 w-full justify-end items-center">
            <form :action="deleteUrl" method="POST" id="deleteHobbyForm">
              @csrf
              @method('DELETE')
              <button type="submit"
                class="px-4 py-2 bg-accent-red/10 text-accent-red rounded hover:bg-accent-red hover:text-white transition-colors duration-150">Delete this hobby</button>
            </form>
            <button @click="deleteModal = false"
              class="px-4 py-2 bg-gray-200 text-ink rounded hover:bg-gray-300 transition-colors duration-150">Cancel</button>
          </div>
        </div>
      </div>


    </section>

    <section
      class="w-full overflow-hidden rounded-lg border border-hairline relative p-4 flex flex-col items-end hover:shadow-md transition-shadow duration-150">
      <h1 class="text-2xl w-full font-semibold text-ink">Recent Activity</h1>

      <div class="w-full my-8 gap-2 flex flex-col">
        @foreach ($recentActivity as $activity)
          <div class="flex flex-row justify-between gap-2 lg:mx-8">
            <p class="text-body-mid text-ink"><span class="text-ink-muted">●</span> {{ $activity->user->name }}
              {{ $activity->action }}</p>
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
