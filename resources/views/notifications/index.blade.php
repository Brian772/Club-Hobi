@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl py-4">
  <div class="mb-6 flex items-center justify-between gap-3">
    <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Notifikasi</h1>
    @if(auth()->user()->notifications()->where('is_read', false)->exists())
      <form action="{{ route('notifications.read-all') }}" method="POST">
        @csrf
        @method('PATCH')
        <button type="submit" class="rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 transition hover:border-primary/40 hover:text-primary">
          Tandai semua terbaca
        </button>
      </form>
    @endif
  </div>

  <div class="space-y-4">
    @forelse ($notifications as $notification)
      <div class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm {{ $notification->is_read ? 'opacity-70' : '' }}">
        <div class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-full {{ $notification->type === 'message' ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-600' }}">
          @if($notification->type === 'message')
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8-1.41 0-2.74-.33-3.9-.92L3 20l1.28-4.58A7.96 7.96 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
          @else
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9" /></svg>
          @endif
        </div>

        <div class="min-w-0 flex-1">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-semibold text-gray-900">{{ $notification->title }}</h2>
            @if(!$notification->is_read)
              <span class="inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
            @endif
          </div>
          <p class="mt-1 text-sm text-gray-600">{{ $notification->content }}</p>
          <div class="mt-2 flex items-center justify-between gap-3 text-xs text-gray-400">
            <span>{{ $notification->created_at ? $notification->created_at->diffForHumans() : '-' }}</span>
            @if(!$notification->is_read)
              <form action="{{ route('notifications.read', ['id' => $notification->id]) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-[11px] font-medium text-primary hover:text-primary/80">Tandai terbaca</button>
              </form>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center text-gray-500">
        Belum ada pemberitahuan.
      </div>
    @endforelse
  </div>

  @if($notifications->hasPages())
    <div class="mt-6">
      {{ $notifications->links() }}
    </div>
  @endif
</div>
@endsection