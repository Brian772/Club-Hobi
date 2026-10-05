@extends('layouts.app')

@section('title', 'Orbii | ' . $otherUser->name ?? 'Pengguna')

@section('content')
  @php
    $currentUserId = Auth::id();
    $otherInitial = strtoupper(substr($otherUser->name ?? 'U', 0, 1));
    $myInitial = strtoupper(substr(Auth::user()->name ?? 'U', 0, 1));
  @endphp

  <div class="flex flex-col h-[calc(100vh-8.5rem)] max-w-5xl mx-auto -mt-4" x-data="{
      openReportModal: false,
      contentType: null,
      contentId: null,
      reportedUserId: null,
      reportUrl: null,
      reportTarget: null,
  
      openReport(type, contentId, reportedUserId = null, url = null, target = null) {
          this.contentType = type;
          this.contentId = contentId;
          this.reportedUserId = reportedUserId;
          this.reportUrl = url;
          this.reportTarget = target;
          this.openReportModal = true;
      },
  
      closeReport() {
          this.contentType = null;
          this.contentId = null;
          this.reportedUserId = null;
          this.reportUrl = null;
          this.openReportModal = false;
      },
  }">
    {{-- Chat Header matching Figma --}}
    <div
      class="flex items-center justify-between pb-4 border-b border-gray-100 bg-white/50 backdrop-blur-sm sticky top-0 z-10 px-2 pt-2">
      <div class="flex items-center gap-3">
        {{-- Back Button --}}
        <a href="{{ route('messages.index') }}" class="p-2 -ml-2 rounded-full hover:bg-gray-100 text-gray-700 transition"
          title="Kembali ke Pesan">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
          </svg>
        </a>

        {{-- Partner Avatar Initial --}}
        @if ($otherUser->avatar_full_url)
          <img src="{{ $otherUser->avatar_full_url }}" alt="{{ $otherUser->name }}"
            class="rounded-full size-10 object-cover"
            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
          </img>
          <div
            class="hidden size-10 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
            {{ Str::upper(Str::substr($otherUser->name, 0, 1)) }}
          </div>
        @else
          <div
            class="size-10 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
            {{ Str::upper(Str::substr($otherUser->name, 0, 1)) }}
          </div>
        @endif

        {{-- Partner Name --}}
        <div>
          <h2 class="text-base font-bold text-gray-900 leading-tight">{{ $otherUser->name }}</h2>
          <span data-presence-user="{{ $otherUser->id }}"
            class="inline-flex items-center gap-1.5 text-[11px] text-gray-500">
            <span data-presence-dot
              class="h-2 w-2 rounded-full {{ $otherUser->isOnline() ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
            <span data-presence-label>{{ $otherUser->isOnline() ? 'Online' : 'Offline' }}</span>
          </span>
        </div>
      </div>

      {{-- Three Dots Menu --}}
      <div class="relative" x-data="{ MenuOpen: false }">
        <button @click="MenuOpen = !MenuOpen" type="button"
          class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="5" r="2" />
            <circle cx="12" cy="12" r="2" />
            <circle cx="12" cy="19" r="2" />
          </svg>
        </button>
        <div x-show="MenuOpen" x-cloak @keydown.escape.window="MenuOpen = false">
          <div x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
            @click.outside="MenuOpen = false" @click="MenuOpen = false"
            class="absolute z-50 right-5 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
            <a href="{{ route('profile.show', ['user' => $otherUser->id]) }}"
              class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-user-round">
                <circle cx="12" cy="8" r="5" />
                <path d="M20 21a8 8 0 0 0-16 0" />
              </svg>
              Lihat Profil
            </a>
            <a href="{{ route('messages.index') }}"
              class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-message-circle-icon lucide-message-circle">
                <path
                  d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719" />
              </svg>
              Keluar Obrolan
            </a>
            <div class="h-px border-b border-hairline my-2"></div>
            <button type="button"
              @click="
                openReport(
                  'user',
                  '{{ $otherUser->id }}',
                  '{{ $otherUser->id }}',
                  '{{ route('reports.store') }}',
                  @js([
    'name' => $otherUser->name,
    'avatar' => $otherUser->avatar_full_url ?? asset('images/default-avatar.svg'),
    'joined' => $otherUser->created_at->format('d M Y'),
])
              )"
              class="flex flex-row gap-2 items-center w-full cursor-pointer px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-flag">
                <path
                  d="M4 22V4a1 1 0 0 1 .4-.8A6 6 0 0 1 8 2c3 0 5 2 7.333 2q2 0 3.067-.8A1 1 0 0 1 20 4v10a1 1 0 0 1-.4.8A6 6 0 0 1 16 16c-3 0-5-2-8-2a6 6 0 0 0-4 1.528" />
              </svg>
              Laporkan Pengguna
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Chat Messages Area --}}
    <div id="chatMessages" data-chat-updates-url="{{ route('messages.updates', $otherUser->id) }}"
      data-current-user-id="{{ $currentUserId }}" data-current-initial="{{ $myInitial }}"
      data-other-initial="{{ $otherInitial }}" data-after="{{ $messages->last()?->send_at?->toIso8601String() }}"
      class="flex-1 overflow-y-auto py-6 px-2 space-y-4">
      @if ($messages->isEmpty())
        <div data-chat-empty class="flex flex-col items-center justify-center h-full text-center text-gray-400">
          <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-2">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-600">Belum ada pesan sebelumnya.</p>
          <p class="text-xs text-gray-400 mt-1">Kirim pesan pertama untuk memulai obrolan dengan {{ $otherUser->name }}!
          </p>
        </div>
      @else
        @foreach ($messages as $msg)
          @php
            $isMe = $msg->sender_id === $currentUserId;
          @endphp

          @if (!$isMe)
            {{-- Received Message (Left) --}}
            <div data-message-id="{{ $msg->id }}" class="flex items-start gap-2.5 max-w-[80%] md:max-w-[70%]">
              {{-- Partner Initial Avatar --}}
              @if ($otherUser->avatar_full_url)
                <img src="{{ $otherUser->avatar_full_url }}" alt="{{ $otherUser->name }}"
                  class="rounded-full size-8 object-cover"
                  onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                <div
                  class="hidden size-8 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
                  {{ Str::upper(Str::substr($otherUser->name, 0, 1)) }}
                </div>
              @else
                <div
                  class="size-8 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
                  {{ Str::upper(Str::substr($otherUser->name, 0, 1)) }}
                </div>
              @endif
              <div>
                <div
                  class="bg-white text-gray-900 text-sm px-4 py-2.5 rounded-2xl rounded-tl-sm shadow-sm border border-gray-100">
                  {{ $msg->content }}
                </div>
                <span class="text-[10px] text-gray-400 ml-2 mt-1 block">
                  {{ $msg->send_at ? $msg->send_at->format('H:i') : '' }}
                </span>
              </div>
            </div>
          @else
            {{-- Sent Message (Right) --}}
            <div data-message-id="{{ $msg->id }}"
              class="flex items-start justify-end gap-2.5 max-w-[80%] md:max-w-[70%] ml-auto">
              <div class="flex flex-col items-end">
                <div class="bg-[#0070F3] text-white text-sm px-4 py-2.5 rounded-2xl rounded-tr-sm shadow-sm">
                  {{ $msg->content }}
                </div>
                <span class="text-[10px] text-gray-400 mr-2 mt-1 block">
                  {{ $msg->send_at ? $msg->send_at->format('H:i') : '' }}
                </span>
              </div>
              {{-- Current User Initial Avatar --}}
              @if (auth()->user()->avatar_full_url)
                <img src="{{ auth()->user()->avatar_full_url }}" alt="{{ auth()->user()->name }}"
                  class="rounded-full size-8 object-cover"
                  onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                <div
                  class="hidden size-8 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
                  {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
                </div>
              @else
                <div
                  class="size-8 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
                  {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
                </div>
              @endif
            </div>
          @endif
        @endforeach
      @endif
    </div>

    {{-- Chat Input Form matching Figma --}}
    <div class="pt-3 pb-1 border-t border-gray-100 bg-white/70 backdrop-blur-sm">
      <form action="{{ route('messages.store', $otherUser->id) }}" method="POST" data-turbo="false"
        class="w-full flex items-center gap-3" id="chatForm">
        @csrf
        <input type="text" name="content" id="messageInput" required autocomplete="off"
          placeholder="Ketik Pesan..."
          class="flex-1 bg-white border border-gray-200 rounded-full py-3.5 pl-6 pr-4 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm transition">

        <button type="submit"
          class="shrink-0 w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 active:scale-95 text-gray-900 transition">
          <svg class="w-5 h-5 -rotate-45" fill="currentColor" viewBox="0 0 24 24">
            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
          </svg>
        </button>
      </form>
    </div>
    <x-report-modal />
  </div>

@endsection
