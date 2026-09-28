@extends('layouts.app')

@section('content')
@php
  $currentUserId = Auth::id();
  $otherInitial = strtoupper(substr($otherUser->name ?? 'U', 0, 1));
  $myInitial = strtoupper(substr(Auth::user()->name ?? 'U', 0, 1));
@endphp

<div class="flex flex-col h-[calc(100vh-8.5rem)] max-w-5xl mx-auto -mt-4">
  {{-- Chat Header matching Figma --}}
  <div class="flex items-center justify-between pb-4 border-b border-gray-100 bg-white/50 backdrop-blur-sm sticky top-0 z-10 px-2 pt-2">
    <div class="flex items-center gap-3">
      {{-- Back Button --}}
      <a href="{{ route('messages.index') }}" 
         class="p-2 -ml-2 rounded-full hover:bg-gray-100 text-gray-700 transition" 
         title="Kembali ke Pesan">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
      </a>

      {{-- Partner Avatar Initial --}}
      @if($otherUser->avatar_full_url && !str_contains($otherUser->avatar_full_url, 'people'))
        <img src="{{ $otherUser->avatar_full_url }}" alt="{{ $otherUser->name }}" class="w-10 h-10 rounded-full object-cover shrink-0">
      @else
        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-700 font-semibold flex items-center justify-center text-sm shrink-0 select-none">
          {{ $otherInitial }}
        </div>
      @endif

      {{-- Partner Name --}}
      <div>
        <h2 class="text-base font-bold text-gray-900 leading-tight">{{ $otherUser->name }}</h2>
        <span class="text-[11px] text-gray-400">{{ $otherUser->role_global === 'admin' ? 'Admin' : 'Member' }}</span>
      </div>
    </div>

    {{-- Three Dots Menu --}}
    <div class="relative" x-data="{ open: false }">
      <button @click="open = !open" type="button" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <circle cx="12" cy="5" r="2" />
          <circle cx="12" cy="12" r="2" />
          <circle cx="12" cy="19" r="2" />
        </svg>
      </button>
      <div x-show="open" @click.outside="open = false" x-cloak
           class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-20">
        <a href="{{ route('profile.dashboard') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">
          Lihat Profil
        </a>
        <a href="{{ route('messages.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">
          Tutup Obrolan
        </a>
      </div>
    </div>
  </div>

  {{-- Chat Messages Area --}}
  <div id="chatMessages" class="flex-1 overflow-y-auto py-6 px-2 space-y-4">
    @if($messages->isEmpty())
      <div class="flex flex-col items-center justify-center h-full text-center text-gray-400">
        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-2">
          <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
        </div>
        <p class="text-sm font-medium text-gray-600">Belum ada pesan sebelumnya.</p>
        <p class="text-xs text-gray-400 mt-1">Kirim pesan pertama untuk memulai obrolan dengan {{ $otherUser->name }}!</p>
      </div>
    @else
      @foreach($messages as $msg)
        @php
          $isMe = $msg->sender_id === $currentUserId;
        @endphp

        @if(!$isMe)
          {{-- Received Message (Left) --}}
          <div class="flex items-start gap-2.5 max-w-[80%] md:max-w-[70%]">
            {{-- Partner Initial Avatar --}}
            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 font-semibold flex items-center justify-center text-xs shrink-0 select-none mt-0.5">
              {{ $otherInitial }}
            </div>
            <div>
              <div class="bg-white text-gray-900 text-sm px-4 py-2.5 rounded-2xl rounded-tl-sm shadow-sm border border-gray-100">
                {{ $msg->content }}
              </div>
              <span class="text-[10px] text-gray-400 ml-2 mt-1 block">
                {{ $msg->send_at ? $msg->send_at->format('H:i') : '' }}
              </span>
            </div>
          </div>
        @else
          {{-- Sent Message (Right) --}}
          <div class="flex items-start justify-end gap-2.5 max-w-[80%] md:max-w-[70%] ml-auto">
            <div class="flex flex-col items-end">
              <div class="bg-[#0070F3] text-white text-sm px-4 py-2.5 rounded-2xl rounded-tr-sm shadow-sm">
                {{ $msg->content }}
              </div>
              <span class="text-[10px] text-gray-400 mr-2 mt-1 block">
                {{ $msg->send_at ? $msg->send_at->format('H:i') : '' }}
              </span>
            </div>
            {{-- Current User Initial Avatar --}}
            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 font-semibold flex items-center justify-center text-xs shrink-0 select-none mt-0.5">
              {{ $myInitial }}
            </div>
          </div>
        @endif
      @endforeach
    @endif
  </div>

  {{-- Chat Input Form matching Figma --}}
  <div class="pt-3 pb-1 border-t border-gray-100 bg-white/70 backdrop-blur-sm">
    <form action="{{ route('messages.store', $otherUser->id) }}" method="POST" data-turbo="false" class="relative flex items-center" id="chatForm">
      @csrf
      <input type="text" 
             name="content" 
             id="messageInput"
             required
             autocomplete="off"
             placeholder="Ketik Pesan..." 
             class="w-full bg-white border border-gray-200 rounded-full py-3.5 pl-6 pr-14 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm transition">
      
      {{-- Send Button with Figma Arrow Icon --}}
      <button type="submit" 
              class="absolute right-2.5 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 active:scale-95 text-gray-900 transition">
        <svg class="w-5 h-5 -rotate-45 translate-x-0.5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
        </svg>
      </button>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const chatContainer = document.getElementById('chatMessages');
    const messageInput = document.getElementById('messageInput');
    
    // Auto scroll to bottom
    if (chatContainer) {
      chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Auto focus message input
    if (messageInput) {
      messageInput.focus();
    }
  });
</script>
@endsection
