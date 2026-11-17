@extends('layouts.app')

@section('title', 'Orbii | Audit Logs')

@section('content')
  <header class="mb-4 flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
    <h1 class="text-2xl text-ink font-semibold">Audit Logs</h1>

    <form method="GET" class="w-full lg:w-1/3">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by action or target type"
        class="px-4 py-2 border border-hairline rounded-md w-full">
    </form>
  </header>

  <main class="border border-hairline p-4 rounded-lg">
    <div class="flex flex-col gap-4">
      <div id="container" class="border mt-2 border-hairline p-2 rounded-lg overflow-x-auto lg:overflow-visible">
        <table class="min-w-full h-max table-auto">
          <thead class="border-b border-hairline">
            <tr>
              <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                Actor</th>
              <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                Action</th>
              <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                Target</th>
              <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                Date</th>
              <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($logs as $log)
              <tr class="odd:bg-gray-100 even:bg-canvas">
                <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $log->user?->name ?? 'System' }}</td>
                <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $log->action }}</td>
                <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $log->target_type ?? 'N/A' }}</td>
                <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $log->created_at->format('d M Y, h:i') }}
                </td>
                <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">
                  <div x-data="{ MenuOpen: false }" class="relative">
                    <button type="button" x-ref="button" @click="MenuOpen = true"
                      class="text-ink-muted text-body-mid rounded-full p-2 hover:bg-hairline">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-ellipsis-vertical">
                        <circle cx="12" cy="12" r="1" />
                        <circle cx="12" cy="5" r="1" />
                        <circle cx="12" cy="19" r="1" />
                      </svg>
                    </button>

                    <div x-show="MenuOpen" x-cloak @keydown.escape.window="MenuOpen = false">
                      <div x-anchor.noflip="$refs.button" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="MenuOpen = false"
                        @click="MenuOpen = false"
                        class="z-50 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
                        <a href="{{ route('admin.audit-logs.show', $log->id) }}"
                          class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-info">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4" />
                            <path d="M12 8h.01" />
                          </svg>
                          View Detail
                        </a>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-4 h-16 whitespace-nowrap text-caption text-center">
                  No audit logs found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-4">
        {{ $logs->links() }}
      </div>
    </div>
  </main>
@endsection
