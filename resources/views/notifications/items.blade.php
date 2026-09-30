@forelse ($notifications as $notification)
  <div data-notification-item="{{ $notification->id }}" class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm {{ $notification->is_read ? 'opacity-70' : '' }}">
    <div class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-full {{ $notification->type === 'message' ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-600' }}">
      @if($notification->type === 'message')
        @include('layouts.partials.icons.chat')
      @else
        @include('layouts.partials.icons.notif')
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