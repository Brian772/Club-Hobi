@extends('layouts.app')

@section('title', 'Orbii | Notifikasi')

@section('content')
  <div class="mx-auto max-w-7xl py-4">
    <div class="mb-6 flex items-center justify-between gap-3">
      <h1 class="text-title lg:text-2xl font-semibold tracking-tight text-ink">Notifikasi</h1>
      <form data-notification-mark-all action="{{ route('notifications.read-all') }}" method="POST"
        class="{{ auth()->user()->notifications()->where('is_read', false)->exists() ? '' : 'hidden' }}">
        @csrf
        @method('PATCH')
        <button type="submit"
          class="rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 transition hover:border-primary/40 hover:text-primary">
          Tandai semua terbaca
        </button>
      </form>
    </div>

    <div data-notification-page-list class="space-y-4">
      @include('notifications.items', ['notifications' => $notifications])
    </div>

    @if ($notifications->hasPages())
      <div class="mt-6">
        {{ $notifications->links() }}
      </div>
    @endif
  </div>
@endsection
