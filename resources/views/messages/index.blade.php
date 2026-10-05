@extends('layouts.app')

@section('title', 'Orbii | Messages')

@section('content')
  <div class="max-w-7xl mx-auto py-2">
    <div class="mb-6">
      <h1 class="text-title lg:text-2xl font-semibold text-ink tracking-tight">Pesan</h1>
    </div>

    @if ($conversations->isEmpty())
      {{-- Empty State --}}
      <div class="bg-white rounded-2xl p-8 border border-hairline shadow-sm text-center">
        <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-ink mb-1">Belum Ada Percakapan</h3>
        <p class="text-sm text-ink-muted mb-6">Mulai obrolan dengan anggota klub atau pengguna lainnya.</p>
      </div>
    @else
      {{-- Conversation List matching Figma --}}
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
        @foreach ($conversations as $conv)
          @php
            $partner = $conv->user;
            $initial = strtoupper(substr($partner->name ?? 'U', 0, 1));
            $lastContent = $conv->last_message ? $conv->last_message->content : 'Memulai percakapan...';
            $timeAgo = $conv->last_message
                ? $conv->last_message->send_at->diffForHumans([
                    'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                    'short' => true,
                ])
                : '';
            // Clean Indonesian relative time like "2 hari lalu"
            $timeAgo = str_replace(' yang lalu', ' lalu', $timeAgo);
          @endphp
          <a href="{{ route('messages.show', $partner->id) }}"
            class="flex items-center justify-between p-4 hover:bg-gray-50/80 transition duration-150 group">
            <div class="flex items-center gap-3.5 min-w-0">
              {{-- Avatar Circle --}}
              @if ($partner->avatar_full_url && !str_contains($partner->avatar_full_url, 'people'))
                <img src="{{ $partner->avatar_full_url }}" alt="{{ $partner->name }}"
                  class="w-11 h-11 rounded-full object-cover shrink-0">
              @else
                <div
                  class="w-11 h-11 rounded-full bg-gray-200/90 text-gray-600 font-semibold flex items-center justify-center text-sm shrink-0 select-none group-hover:bg-primary/20 group-hover:text-primary transition">
                  {{ $initial }}
                </div>
              @endif

              {{-- Text Info --}}
              <div class="min-w-0">
                <div class="flex items-center gap-2">
                  <span class="font-semibold text-ink text-sm group-hover:text-primary transition truncate">
                    {{ $partner->name }}
                  </span>
                  <span data-presence-user="{{ $partner->id }}"
                    class="inline-flex items-center gap-1 text-[10px] text-ink-muted">
                    <span data-presence-dot
                      class="h-2 w-2 rounded-full {{ $partner->isOnline() ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                    <span data-presence-label>{{ $partner->isOnline() ? 'Online' : 'Offline' }}</span>
                  </span>
                  @if ($conv->unread_count > 0)
                    <span class="w-2 h-2 rounded-full bg-primary inline-block"></span>
                  @endif
                </div>
                <p
                  class="text-xs text-gray-500 truncate max-w-sm sm:max-w-md mt-0.5 {{ $conv->unread_count > 0 ? 'font-semibold text-gray-800' : '' }}">
                  {{ $lastContent }}
                </p>
              </div>
            </div>

            {{-- Timestamp & Badge --}}
            <div class="flex flex-col items-end gap-1 shrink-0 ml-4">
              <span class="text-xs text-gray-400 font-normal whitespace-nowrap">
                {{ $timeAgo }}
              </span>
              @if ($conv->unread_count > 0)
                <span class="px-1.5 py-0.5 bg-primary text-white text-[10px] font-bold rounded-full">
                  {{ $conv->unread_count }}
                </span>
              @endif
            </div>
          </a>
        @endforeach
      </div>
    @endif

    {{-- Suggested Users / Mulai Percakapan Baru --}}
    @if ($suggestedUsers->isNotEmpty())
      <div class="mt-8">
        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3 px-1">Teman yang bisa diajak mengobrol
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          @foreach ($suggestedUsers as $sUser)
            @php $sInitial = strtoupper(substr($sUser->name ?? 'U', 0, 1)); @endphp
            <a href="{{ route('messages.show', $sUser->id) }}"
              class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:border-primary/40 hover:shadow-sm transition group">
              <div
                class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 font-semibold flex items-center justify-center text-sm shrink-0 group-hover:bg-primary/10 group-hover:text-primary">
                {{ $sInitial }}
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-gray-900 truncate group-hover:text-primary">{{ $sUser->name }}</p>
                <p data-presence-user="{{ $sUser->id }}"
                  class="inline-flex items-center gap-1 text-[11px] text-gray-400">
                  <span data-presence-dot
                    class="h-1.5 w-1.5 rounded-full {{ $sUser->isOnline() ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                  <span data-presence-label>{{ $sUser->isOnline() ? 'Online' : 'Offline' }}</span>
                </p>
              </div>
              <svg class="w-4 h-4 text-gray-400 group-hover:text-primary shrink-0" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>
@endsection
