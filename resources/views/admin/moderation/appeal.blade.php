@extends('layouts.app')

@section('content')
  <div x-data="{ OpenApproveModal: false, OpenRejectModal: false }">
    <header class="flex flex-row gap-1 lg:gap-4 items-center justify-start mb-6">
      <a href="{{ route('admin.moderation') }}" class="text-ink-muted">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
          <path d="M0 0h16v9H0z" fill="none" />
          <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
          <path fill="currentColor"
            d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
        </svg>
      </a>
      <h2 class="text-title lg:text-heading-2 flex flex-row items-center gap-2 justify-center text-ink">Appeal Detail
        @if ($appeal->status === 'pending')
          <span
            class="rounded-full bg-accent-yellow/10 text-accent-yellow text-overline px-2 py-1">{{ Str::upper($appeal->status) }}</span>
        @elseif ($appeal->status === 'approved')
          <span
            class="rounded-full bg-accent-green/10 text-accent-green text-overline px-2 py-1">{{ Str::upper($appeal->status) }}</span>
        @else
          <span
            class="rounded-full bg-accent-red/10 text-accent-red text-overline px-2 py-1">{{ Str::upper($appeal->status) }}</span>
        @endif
      </h2>
    </header>

    <div class="bg-canvas border border-hairline rounded-lg p-6">
      <div
        class="hidden lg:flex lg:flex-row items-center justify-between mb-6 gap-2 p-2 border border-hairline rounded-lg">
        <div class="flex flex-row items-center gap-2 p-2">
          <img src="{{ $user->avatar_full_url ?? asset('images/default-avatar.png') }}" alt="{{ $user->name }}"
            class="w-9 h-9 rounded-full object-cover">
          <div class="flex flex-col">
            <p class="text-body-mid text-ink font-semibold">{{ $user->name }}</p>
            <span class="text-caption text-ink-muted">{{ $user->email }}</span>
          </div>
        </div>
        <div class="p-2">
          <span class="text-ink-muted text-caption">Bergabung</span>
          <p class="text-ink text-caption">{{ $user->created_at }}</p>
        </div>
      </div>
      <div class="flex flex-col gap-2">
        <div class="flex flex-row gap-2 lg:hidden items-center">
          <span class="text-caption text-ink-faint">User:</span>
          <span class="text-caption text-ink">{{ $appeal->user->name }}</span>
        </div>
        <div class="flex flex-row gap-2 lg:hidden items-center">
          <span class="text-caption text-ink-faint">Email:</span>
          <span class="text-caption text-ink">{{ $appeal->user->email }}</span>
        </div>
        <div class="flex flex-row gap-2 items-center">
          <span class="text-caption text-ink-faint">Status Account:</span>
          <span class="text-caption text-ink">{{ $appeal->user->status }}</span>
        </div>
        <div class="flex flex-row gap-2 items-center">
          <span class="text-caption text-ink-faint">Submitted:</span>
          <span class="text-caption text-ink">{{ $appeal->created_at->format('d M Y') }}</span>
        </div>
        <div class="flex flex-row gap-2 items-center">
          <span class="text-caption text-ink-faint">Status:</span>
          @if ($appeal->status === 'pending')
            <span class="text-caption text-accent-yellow">{{ $appeal->status }}</span>
          @elseif ($appeal->status === 'approved')
            <span class="text-caption text-accent-green">{{ $appeal->status }}</span>
          @else
            <span class="text-caption text-accent-red">{{ $appeal->status }}</span>
          @endif
        </div>
        <div class="flex flex-col mt-4 gap-1 items-start">
          <span class="text-caption text-ink-faint">Appeal reason:</span>
          <span class="text-caption text-ink">{{ $appeal->reason }}</span>
        </div>
        @if ($appeal->status === 'rejected')
        <div class="flex flex-col gap-1 items-start">
          <span class="text-caption text-ink-faint">Reject reason:</span>
          <span class="text-caption text-ink">{{ $appeal->admin_note }}</span>
        </div>
        @endif
      </div>
    </div>

    @if ($appeal->status === 'pending')
      <div class="flex flex-row gap-2 mt-4">
        <button type="button" @click="OpenApproveModal = true"
          class="bg-primary/10 text-primary text-caption px-4 py-2 rounded-md hover:bg-primary hover:text-white">Approve
          Appeal</button>
        <button type="button" @click="OpenRejectModal = true"
          class="bg-accent-red/10 text-accent-red text-caption px-4 py-2 rounded-md hover:bg-accent-red hover:text-white">Reject
          Appeal</button>
      </div>
    @endif

    <div x-show="OpenApproveModal" x-cloak @keydown.escape.window="OpenApproveModal = false">
      <div class="fixed inset-0 bg-black/50 z-40" @click="OpenApproveModal = false"></div>
      <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="bg-canvas border border-hairline rounded-lg shadow-lg p-6 w-full max-w-md">
          <div class="flex flex-col gap-2 mb-4">
            <h2 class="text-lg font-semibold">Approve this appeal?</h2>
            <p>Are you sure you want to approve this appeal? The appeal will be approved, and the user's account will be
              updated
              accordingly.</p>
          </div>
          <div class="flex justify-end gap-2">
            <form action="{{ route('admin.moderation.appeal.approve', $appeal->id) }}" method="POST">
              @csrf
              @method('PATCH')
              <button type="submit"
                class="px-4 py-2 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded">
                Approve Appeal
              </button>
            </form>
            <button type="button" @click="OpenApproveModal = false"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <div x-show="OpenRejectModal" x-cloak @keydown.escape.window="OpenRejectModal = false">
      <div class="fixed inset-0 bg-black/50 z-40" @click="OpenRejectModal = false"></div>
      <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="bg-canvas border border-hairline rounded-lg shadow-lg p-6 w-full max-w-md">
          <div class="flex flex-col gap-2 mb-4">
            <h2 class="text-lg font-semibold">Reject this appeal?</h2>
            <p>Are you sure you want to reject this appeal? The appeal will be denied, and the user's account will remain
              in its current state.</p>
          </div>
          <form action="{{ route('admin.moderation.appeal.reject', $appeal->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="flex flex-col gap-1 mb-4">
              <label for="admin_note">Reason:</label>
              <textarea name="admin_note" id="admin_note" rows="3" class="border border-hairline rounded-md"
                placeholder="Write the reason for rejecting this appeal" required></textarea>
            </div>
            <div class="flex justify-end gap-2">
              <button type="submit"
                class="px-4 py-2 bg-accent-red/10 text-accent-red hover:bg-accent-red hover:text-white rounded">
                Reject Appeal
              </button>
              <button type="button" @click="OpenRejectModal = false"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
