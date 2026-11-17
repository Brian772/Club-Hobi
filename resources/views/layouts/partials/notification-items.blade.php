@forelse ($notifications as $notif)
  <div data-notification-item="{{ $notif->id }}" class="flex gap-3 {{ $notif->is_read ? 'opacity-60' : '' }}">
    <div class="mt-0.5 shrink-0">
      @if(in_array($notif->type, ['message', 'comment'], true))
        @include('layouts.partials.icons.chat')
      @else
        @include('layouts.partials.icons.notif')
      @endif
    </div>
    <div>
      <p class="text-sm font-semibold text-neutral-900">{{ $notif->title }}</p>
      <p class="text-sm text-neutral-600">{{ $notif->content }}</p>
      <p class="mt-0.5 text-xs text-neutral-400">{{ $notif->created_at->diffForHumans() }}</p>
    </div>
  </div>
@empty
  <p class="py-8 text-center text-sm text-neutral-400">Belum ada notifikasi.</p>
@endforelse