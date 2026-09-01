@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  @php
    $currentUser = $user ?? Auth::user();
    $selected = $selectedConversation ?? null;
  @endphp

  @if (!empty($conversations) && $selected)
    <div class="flex h-[calc(100vh-120px)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <aside class="hidden w-[320px] shrink-0 border-r border-slate-200 bg-slate-50 md:block">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
          <h1 class="text-2xl font-bold text-slate-800">Pesan</h1>
        </div>

        <div class="space-y-2 p-3">
          @foreach ($conversations as $conversation)
            @php
              $active = $selected['id'] === $conversation['id'];
            @endphp
            <a href="{{ route('messages.index', ['conversation' => $conversation['id']]) }}"
               class="flex items-center gap-3 rounded-xl border p-3 transition {{ $active ? 'border-blue-200 bg-blue-50' : 'border-transparent bg-transparent hover:bg-white' }}">
              <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-700">
                {{ strtoupper(substr($conversation['name'], 0, 1)) }}
              </div>

              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2">
                  <span class="truncate font-semibold text-slate-800">{{ $conversation['name'] }}</span>
                  <span class="text-[11px] text-slate-400">{{ $conversation['time'] }}</span>
                </div>
                <p class="truncate text-sm text-slate-500">{{ $conversation['last_message'] }}</p>
              </div>
            </a>
          @endforeach
        </div>
      </aside>

      <section class="flex min-w-0 flex-1 flex-col">
        <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-4 md:px-6">
          <div class="flex items-center gap-3">
            <a href="{{ route('messages.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-600 hover:bg-slate-100 md:hidden">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </a>

            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-700">
              {{ strtoupper(substr($selected['name'], 0, 1)) }}
            </div>

            <div>
              <h2 class="text-lg font-semibold text-slate-800">{{ $selected['name'] }}</h2>
            </div>
          </div>

          <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-500 hover:bg-slate-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01" />
            </svg>
          </button>
        </header>

        <div class="flex flex-1 flex-col justify-between bg-slate-50/60">
          <div class="flex-1 space-y-4 overflow-y-auto px-4 py-5 md:px-6">
            @foreach ($selected['messages'] as $message)
              <div class="flex {{ $message['from'] === 'me' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[70%] rounded-2xl px-4 py-3 text-sm {{ $message['from'] === 'me' ? 'bg-blue-600 text-white' : 'bg-white text-slate-700 shadow-sm ring-1 ring-slate-200' }}">
                  <div class="mb-1 flex items-center gap-2">
                    @if ($message['from'] !== 'me')
                      <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-600">
                        {{ strtoupper(substr($selected['name'], 0, 1)) }}
                      </span>
                    @endif
                    <span class="text-[11px] {{ $message['from'] === 'me' ? 'text-blue-100' : 'text-slate-400' }}">{{ $message['time'] }}</span>
                  </div>
                  <p class="leading-relaxed">{{ $message['text'] }}</p>
                </div>
              </div>
            @endforeach
          </div>

          <form method="POST" action="{{ route('messages.store', ['conversation' => $selected['id']]) }}" class="border-t border-slate-200 bg-white p-3 md:p-4">
            @csrf
            <div class="flex items-center gap-3">
              <input type="text" name="message" value="" placeholder="Ketik Pesan..." class="flex-1 rounded-full border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400 focus:bg-white" required>
              <button type="submit" class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm transition hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </button>
            </div>
          </form>
        </div>
      </section>
    </div>
  @else
    <div class="flex min-h-[60vh] items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
      <div>
        <h2 class="text-xl font-semibold text-slate-700">Belum ada percakapan</h2>
        <p class="mt-2 text-slate-500">Mulai chat dengan pengguna lain untuk memulai forum komunikasi.</p>
      </div>
    </div>
  @endif
@endsection