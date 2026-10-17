@extends('layouts.app')

@section('title', 'Orbii | ' . ($otherUser->name ?? 'Pengguna'))

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

    {{-- Chat Header --}}
    <div class="flex items-center justify-between pb-4 border-b border-gray-100 bg-white/50 backdrop-blur-sm sticky top-0 z-10 px-2 pt-2">
      <div class="flex items-center gap-3">
        <a href="{{ route('messages.index') }}"
          class="p-2 -ml-2 rounded-full hover:bg-gray-100 text-gray-700 transition" title="Kembali ke Pesan">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
          </svg>
        </a>

        @if ($otherUser->avatar_full_url && !str_contains($otherUser->avatar_full_url, 'people'))
          <img src="{{ $otherUser->avatar_full_url }}" alt="{{ $otherUser->name }}"
            class="w-10 h-10 rounded-full object-cover shrink-0">
        @else
          <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-700 font-semibold flex items-center justify-center text-sm shrink-0 select-none">
            {{ $otherInitial }}
          </div>
        @endif

        <div>
          <h2 class="text-base font-bold text-gray-900 leading-tight">{{ $otherUser->name }}</h2>
          <p class="text-[11px] text-gray-500">{{ $club->name }}</p>
          <span data-presence-user="{{ $otherUser->id }}" class="inline-flex items-center gap-1.5 text-[11px] text-gray-500">
            <span data-presence-dot class="h-2 w-2 rounded-full {{ $otherUser->isOnline() ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
            <span data-presence-label>{{ $otherUser->isOnline() ? 'Online' : 'Offline' }}</span>
          </span>
        </div>
      </div>

      {{-- Three Dots Menu --}}
      <div class="relative" x-data="{ menuOpen: false }">
        <button @click="menuOpen = !menuOpen" type="button" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="5" r="2" />
            <circle cx="12" cy="12" r="2" />
            <circle cx="12" cy="19" r="2" />
          </svg>
        </button>
        <div x-show="menuOpen" x-cloak @keydown.escape.window="menuOpen = false" @click.outside="menuOpen = false"
          @click="menuOpen = false"
          class="absolute z-50 right-0 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
          <a href="{{ route('profile.show', ['user' => $otherUser->id]) }}"
            class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
            Lihat Profil
          </a>
          <a href="{{ route('messages.index') }}"
            class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
            Keluar Obrolan
          </a>
          <div class="h-px border-b border-hairline my-2"></div>
          <button type="button"
            @click="openReport(
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
            Laporkan Pengguna
          </button>
        </div>
      </div>
    </div>

    {{-- Chat Messages Area --}}
    <div id="chatMessages"
      data-chat-updates-url="{{ route('messages.updates', [$club->id, $otherUser->id]) }}"
      data-current-user-id="{{ $currentUserId }}"
      data-current-initial="{{ $myInitial }}"
      data-other-initial="{{ $otherInitial }}"
      data-after="{{ $messages->last()?->send_at?->toIso8601String() }}"
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
          <p class="text-xs text-gray-400 mt-1">Kirim pesan pertama untuk memulai obrolan dengan {{ $otherUser->name }}!</p>
        </div>
      @else
        @foreach ($messages as $msg)
          @php $isMe = $msg->sender_id === $currentUserId; @endphp

          @if (!$isMe)
            {{-- Received Message (Left) --}}
            <div data-message-id="{{ $msg->id }}" class="flex items-start gap-2.5 max-w-[80%] md:max-w-[70%]">
              <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 font-semibold flex items-center justify-center text-xs shrink-0 select-none mt-0.5">
                {{ $otherInitial }}
              </div>
              <div>
                <div data-message-content class="bg-white text-gray-900 text-sm px-4 py-2.5 rounded-2xl rounded-tl-sm shadow-sm border border-gray-100">
                  {{ $msg->content }}
                </div>
                <span data-message-time class="text-[10px] text-gray-400 ml-2 mt-1 block">
                  {{ $msg->send_at ? $msg->send_at->format('H:i') : '' }}
                </span>
              </div>
            </div>
          @else
            {{-- Sent Message (Right) --}}
            <div data-message-id="{{ $msg->id }}"
              x-data="{ menuOpen: false, editing: false, confirmDelete: false }" @click.outside="menuOpen = false"
              class="group flex flex-col items-end max-w-[80%] md:max-w-[70%] ml-auto">
              <div class="flex items-start justify-end gap-2.5">
                @if (!$msg->deleted_at)
                  <div data-message-actions class="relative self-center opacity-100 md:opacity-0 md:group-hover:opacity-100 md:group-focus-within:opacity-100 transition-opacity">
                    <button type="button" @click="menuOpen = !menuOpen" title="Opsi pesan" aria-label="Opsi pesan"
                      class="w-7 h-7 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="5" cy="12" r="1.8" />
                        <circle cx="12" cy="12" r="1.8" />
                        <circle cx="19" cy="12" r="1.8" />
                      </svg>
                    </button>
                    <div x-show="menuOpen" x-cloak @keydown.escape.window="menuOpen = false"
                      class="absolute right-full top-0 z-20 mr-1 w-36 overflow-hidden rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                      <button type="button" @click="editing = true; menuOpen = false"
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-xs text-gray-700 hover:bg-gray-50">
                        Edit pesan
                      </button>
                      <button type="button" @click="confirmDelete = true; menuOpen = false"
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-xs text-red-600 hover:bg-red-50">
                        Hapus pesan
                      </button>
                    </div>
                  </div>
                @endif

                <div class="flex flex-col items-end">
                  <div data-message-content class="bg-[#0070F3] text-white text-sm px-4 py-2.5 rounded-2xl rounded-tr-sm shadow-sm">
                    {{ $msg->content }}
                  </div>
                  <span data-message-time class="text-[10px] text-gray-400 mr-2 mt-1 block">
                    {{ $msg->send_at ? $msg->send_at->format('H:i') : '' }}
                    @if ($msg->edited_at && !$msg->deleted_at)
                      <span data-message-edited>(diedit)</span>
                    @endif
                  </span>
                </div>

                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 font-semibold flex items-center justify-center text-xs shrink-0 select-none mt-0.5">
                  {{ $myInitial }}
                </div>
              </div>

              @if (!$msg->deleted_at)
                {{-- Edit form --}}
                <form x-show="editing" x-cloak
                  action="{{ route('messages.update', [$club->id, $otherUser->id, $msg->id]) }}" method="POST"
                  class="mt-2 flex w-full items-center gap-2 rounded-xl border border-gray-200 bg-white p-2 shadow-sm">
                  @csrf
                  @method('PATCH')
                  <input type="text" name="content" value="{{ $msg->content }}" maxlength="5000" required
                    aria-label="Edit pesan"
                    class="min-w-0 flex-1 border-0 bg-transparent px-2 py-1 text-xs text-gray-800 focus:ring-0">
                  <button type="submit" title="Simpan perubahan" aria-label="Simpan perubahan"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-primary hover:bg-primary/10">✓</button>
                  <button type="button" @click="editing = false" title="Batal edit" aria-label="Batal edit"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100">✕</button>
                </form>

                {{-- Delete confirmation --}}
                <div x-show="confirmDelete" x-cloak x-transition.opacity @keydown.escape.window="confirmDelete = false"
                  class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/45 p-4 backdrop-blur-[2px]"
                  role="dialog" aria-modal="true" aria-labelledby="delete-message-title-{{ $msg->id }}">
                  <div @click.stop class="w-full max-w-sm rounded-2xl border border-gray-100 bg-white p-6 shadow-2xl">
                    <h3 id="delete-message-title-{{ $msg->id }}" class="text-center text-base font-semibold text-gray-900">Hapus pesan ini?</h3>
                    <p class="mt-2 text-center text-sm leading-5 text-gray-500">Pesan akan dihapus dari percakapan.</p>
                    <form action="{{ route('messages.destroy', [$club->id, $otherUser->id, $msg->id]) }}" method="POST"
                      class="mt-6 flex gap-3">
                      @csrf
                      @method('DELETE')
                      <button type="button" @click="confirmDelete = false"
                        class="flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</button>
                      <button type="submit"
                        class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">Hapus</button>
                    </form>
                  </div>
                </div>
              @endif
            </div>
          @endif
        @endforeach
      @endif
    </div>

    {{-- Chat Input Form --}}
    <div class="pt-3 pb-1 border-t border-gray-100 bg-white/70 backdrop-blur-sm">
      <form action="{{ route('messages.store', [$club->id, $otherUser->id]) }}" method="POST" data-turbo="false"
        class="w-full flex items-center gap-3" id="chatForm">
        @csrf
        <input type="text" name="content" id="messageInput" required autocomplete="off" placeholder="Ketik Pesan..."
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