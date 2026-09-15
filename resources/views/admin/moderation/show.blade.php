@extends('layouts.app')

@section('content')
  <header class="flex flex-row gap-1 lg:gap-4 items-center justify-start mb-6">
    <a href="{{ route('admin.moderation') }}" class="text-ink-muted">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
        <path d="M0 0h16v9H0z" fill="none" />
        <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
        <path fill="currentColor"
          d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
      </svg>
    </a>
    <h2 class="text-title lg:text-heading-2 flex flex-row items-center gap-2 justify-center text-ink">Report Detail
      @if ($report->status === 'pending')
        <span
          class="rounded-full bg-accent-yellow/10 text-accent-yellow text-overline px-2 py-1">{{ Str::upper($report->status) }}</span>
      @elseif ($report->status === 'resolved')
        <span
          class="rounded-full bg-accent-green/10 text-accent-green text-overline px-2 py-1">{{ Str::upper($report->status) }}</span>
      @else
        <span
          class="rounded-full bg-accent-red/10 text-accent-red text-overline px-2 py-1">{{ Str::upper($report->status) }}</span>
      @endif
    </h2>
  </header>

  <main x-data="{ OpenActionModal: false, OpenIgnoreModal: false }">
    <section class="bg-white rounded-lg border border-hairline p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex flex-col gap-2">
          <span class="text-ink-muted text-body-mid">Reported By</span>
          <span class="text-ink text-body-mid font-semibold">
            {{ $report->reporter->name }}
          </span>
        </div>
        <div class="flex flex-col gap-2">
          <span class="text-ink-muted text-body-mid">Reported User</span>
          <span class="text-ink text-body-mid font-semibold">
            {{ $report->reportedUser->name }}
          </span>
        </div>
        <div class="flex flex-col gap-2">
          <span class="text-ink-muted text-body-mid">Reported At</span>
          <span class="text-ink text-body-mid font-semibold">
            {{ $report->created_at }}
          </span>
        </div>
        <div class="flex flex-col gap-2">
          <span class="text-ink-muted text-body-mid">Report Type</span>
          <span class="text-ink text-body-mid font-semibold">
            {{ $report->content_type }}
          </span>
        </div>
        <div class="flex flex-col md:col-span-2 gap-2">
          <span class="text-ink-muted text-body-mid">Report Reason</span>
          <span class="text-ink text-body-mid font-semibold">
            {{ $report->reason }}
          </span>
        </div>
      </div>
    </section>

    <section class="flex flex-col mt-6 gap-2">
      <h2 class="text-ink text-2xl">Content Detail</h2>
      <div class="bg-white rounded-lg border border-hairline p-6">
        <div class="flex flex-col gap-2">
          <span class="text-ink-muted text-body-mid">Content ID</span>
          <span class="text-ink text-body-mid font-semibold">
            {{ $report->content_id }}
          </span>
        </div>
        @if ($report->content_type === 'user')
          <div class="flex flex-row justify-between items-center mt-4 border border-hairline rounded-lg p-4 ">
            <div class="flex flex-row gap-2 items-center">
              <img src="{{ $report->reportedUser->avatar_full_url }}" alt="{{ $report->reportedUser->name }}'s avatar"
                width="52" height="52" class="border border-hairline w-13 h-13 rounded-full object-cover">
              <div class="flex flex-col gap-1">
                <span class="text-ink text-body font-semibold">{{ $report->reportedUser->name }}</span>
                <span class="text-ink-muted text-body-sm">{{ $report->reportedUser->email }}</span>
              </div>
            </div>
            <div class="flex flex-col gap-1">
              <span class="text-ink-muted text-caption">Joined</span>
              <span
                class="text-ink text-caption font-semibold">{{ $report->reportedUser->created_at->format('d M Y') }}</span>
            </div>
          </div>
        @endif
      </div>
    </section>

    @if ($report->status === 'pending')
      <div class="flex flex-row gap-2 mt-4 ">
        <button type="button" @click="OpenActionModal = true"
          class="bg-primary/10 text-primary px-4 py-2 rounded-md hover:bg-primary hover:text-white">
          Take Action
        </button>
        <button type="button" @click="OpenIgnoreModal = true"
          class="bg-accent-red/10 text-accent-red px-4 py-2 rounded-md hover:bg-accent-red hover:text-white">
          Ignore
        </button>
      </div>
    @endif

    {{-- Action Modal --}}
    <div x-show="OpenActionModal" x-cloak @keydown.escape.window="OpenActionModal = false"
      class="fixed flex items-center justify-center inset-0 z-50">
      <div @click="OpenActionModal = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
      </div>
      <div class="fixed p-6 h-max rounded-lg z-50 bg-canvas border border-hairline overflow-hidden w-sm lg:w-md">
        <div class="flex flex-col gap-2 mb-4">
          <h3 class="text-title text-ink">
            Take Moderation Action
          </h3>
        </div>

        <form action="{{ route('admin.moderation.report.resolved', $report->id) }}" method="POST">
          @csrf
          @method('PATCH')

          <div class="flex flex-col gap-1 mt-2">
            <div class="flex flex-row gap-2 mt-2 items-center">
              <input type="radio" id="suspend" name="action" value="suspend"
                {{ old('action') === 'suspend' ? 'checked' : '' }}>
              <label for="suspend" class="text-ink text-body">Suspend User</label>
            </div>
            <div class="flex flex-row gap-2 mt-2 items-center">
              <input type="radio" id="ban" name="action" value="ban"
                {{ old('action') === 'ban' ? 'checked' : '' }}>
              <label for="ban" class="text-ink text-body">Ban User</label>
            </div>
          </div>

          <div class="flex flex-col gap-1 mt-4">
            <label for="reason">Reason:</label>
            <textarea name="reason" id="reason" class="w-full rounded-md border border-hairline focus:ring-1 focus:ring-primary"
              placeholder="masukan alasan" required></textarea>
          </div>

          <div class="flex flex-row justify-end gap-2 mt-6">
            <button type="submit"
              class="px-4 py-2 text-primary cursor-pointer bg-primary/10 hover:bg-primary rounded hover:text-white">
              Confirm
            </button>
            <button type="button"
              class="bg-gray-200 border border-hairline cursor-pointer rounded text-caption px-4 py-2 text-ink hover:bg-gray-300"
              @click="OpenActionModal = false">Cancel</button>
          </div>
        </form>
      </div>

      {{-- Ignore Modal --}}
      <div x-show="OpenIgnoreModal" x-cloak @keydown.escape.window="OpenIgnoreModal = false"
        class="fixed flex items-center justify-center inset-0 z-50">
        <div @click="OpenIgnoreModal = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
        </div>
        <div class="fixed p-6 h-max rounded-lg z-50 w-max bg-canvas border border-hairline overflow-hidden max-w-md">
          <div class="flex flex-col gap-2 mb-4">
            <h3 class="text-title mb-4 text-ink">
              Ignore Join Request?
            </h3>
            <p class="text-body-mid mb-4 text-ink">Apakah Anda yakin ingin mengabaikan permintaan bergabung ini? Tindakan
              ini tidak dapat dibatalkan.</p>
          </div>
          <div class="flex flex-row justify-end gap-2">
            <form action="{{ route('admin.moderation.report.ignored', $report->id) }}" method="POST">
              @csrf
              @method('PATCH')
              <button type="submit"
                class="px-4 py-2 text-accent-red cursor-pointer bg-accent-red/10 hover:bg-accent-red rounded hover:text-white">
                Ignore
              </button>
            </form>
            <button type="button"
              class="bg-gray-200 border border-hairline cursor-pointer rounded text-caption px-4 py-2 text-ink hover:bg-gray-300"
              @click="OpenIgnoreModal = false">Cancel</button>
          </div>
        </div>
      </div>
  </main>
@endsection
