@extends('layouts.app')

@section('content')
  <header class="mb-4 flex flex-col gap-1">
    <h1 class="text-2xl font-bold text-ink">Moderation</h1>
    <p class="text-body-mid text-ink-muted">Review reports and manage reported content and users.</p>
  </header>

  {{-- Reports & Appeals card --}}
  <section class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-4">
    <div class="w-full h-full flex flex-col border border-hairline rounded-lg p-4">
      <h3 class="text-body-mid text-ink-muted">Report pending</h3>
      <span class="text-2xl font-bold text-ink mr-8">{{ $reports->where('status', 'pending')->count() }}</span>
    </div>
    <div class="w-full h-full flex flex-col border border-hairline rounded-lg p-4">
      <h3 class="text-body-mid text-ink-muted">Report reviewed</h3>
      <span
        class="text-2xl font-bold text-ink mr-8">{{ $reports->whereIn('status', ['ignored', 'resolved'])->count() }}</span>
    </div>
    <div class="w-full h-full flex flex-col border border-hairline rounded-lg p-4">
      <h3 class="text-body-mid text-ink-muted">Appeals pending</h3>
      <span class="text-2xl font-bold text-ink mr-8">{{ $appeals->where('status', 'pending')->count() }}</span>
    </div>
    <div class="w-full h-full flex flex-col border border-hairline rounded-lg p-4">
      <h3 class="text-body-mid text-ink-muted">Appeals reviewed</h3>
      <span class="text-2xl font-bold text-ink mr-8">{{ $appeals->whereIn('status', ['approved', 'rejected'])->count() }}</span>
    </div>
  </section>

  {{-- Reports & Appeals tables --}}
  <main x-data="{ tab: 'reports' }" class="flex flex-col mt-4 rounded-lg border border-hairline p-2 lg:p-4">
    <div class="flex flex-row gap-2 border-b border-hairiline overflow-auto">
      <button @click="tab = 'reports'"
        :class="tab === 'reports' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted'"
        class="px-4 py-2 text-body font-semibold focus:outline-none">
        Reports
        @if ($reports->where('status', 'pending')->count() > 0)
            <span
              class="ml-2 inline-flex items-center justify-center py-1 px-2 text-overline font-semiboldleading-none text-white bg-accent-red rounded-full">
              {{ $reports->where('status', 'pending')->count() }}
            </span>
          @endif
      </button>
      <button @click="tab = 'appeals'"
        :class="tab === 'appeals' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted'"
        class="px-4 py-2 text-body font-semibold focus:outline-none">
        Appeals
        @if ($appeals->where('status', 'pending')->count() > 0)
            <span
              class="ml-2 inline-flex items-center justify-center py-1 px-2 text-overline font-semiboldleading-none text-white bg-accent-red rounded-full">
              {{ $appeals->where('status', 'pending')->count() }}
            </span>
          @endif
      </button>
    </div>

    {{-- Reports Tab --}}
    <section x-show="tab === 'reports'" class="mt-4">
      @if ($reports->isEmpty())
        <p class="text-caption text-ink-muted mt-4">Tidak ada laporan.</p>
      @else
        <div id="container" class="border mt-2 border-hairline p-2 rounded-lg overflow-x-auto lg:overflow-visible">
          <table class="min-w-full h-max table-auto">
            <thead class="border-b border-hairline">
              <tr>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  Reporter</th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  Reported User</th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  Content Type</th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  Date</th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  Status</th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($reports as $report)
                <tr class="odd:bg-gray-100 even:bg-canvas">
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $report->reporter->name }}</td>
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $report->reportedUser->name }}</td>
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $report->content_type }}</td>
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $report->created_at->format('d M Y') }}
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $report->status }}</td>
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
                          <a href="{{ route('admin.moderation.report.show', $report->id) }}"
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
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </section>

    {{-- Appeals Tab --}}
    <section x-show="tab === 'appeals'" class="mt-4">
      @if ($reports->isEmpty())
        <p class="text-caption text-ink-muted mt-4">Tidak ada laporan.</p>
      @else
        <div id="container" class="border mt-2 border-hairline p-2 rounded-lg overflow-x-auto lg:overflow-visible">
          <table class="min-w-full h-max table-auto">
            <thead class="border-b border-hairline">
              <tr>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  User</th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  Status Account</th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  Submited</th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  Status</th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($appeals as $appeal)
                <tr class="odd:bg-gray-100 even:bg-canvas">
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $appeal->user->name }}</td>
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $appeal->user->status }}</td>
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $appeal->created_at->format('d M Y') }}
                  </td>
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $appeal->status }}</td>
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
                          <a href="{{ route('admin.moderation.appeal.show', $appeal->id) }}"
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
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </section>
  </main>
@endsection
