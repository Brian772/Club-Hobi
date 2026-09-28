@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <a href="{{ route('clubs.index') }}" class="flex w-max items-center gap-2 text-ink-muted mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
      <path d="M0 0h16v9H0z" fill="none" />
      <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
      <path fill="currentColor"
        d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
    </svg>
  </a>
  <section class="flex flex-col gap-4">
    <header class="flex flex-col justify-center items-start md:items-stretch md:flex-row w-full pb-4">
      <img src="{{ $profile->avatar_full_url }}" alt="Logo {{ $profile->name }}"
        class="rounded-md w-full md:w-100 h-48 md:h-auto object-cover mb-4 md:mb-0 md:mr-4 border border-hairline">
      <div class="flex flex-col justify-between items-start w-full self-stretch">
        <div class="flex flex-col gap-2">
          <div class="flex flex-col gap-2 mb-2">
            <h1 class="text-heading-2 text-ink font-bold">{{ $profile->name }}</h1>
          </div>
        </div>
      </div>
    </header>

    <main>
      @if ($user->id === $profile->id)
        <div class="flex flex-col gap-4">
          <a href="{{ route('profile.edit') }}"
            class="text-caption text-primary flex flex-row gap-2 justify-center items-center border border-primary bg-primary/10 rounded-md px-4 py-2 hover:bg-primary hover:text-white w-max">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-edit lucide-edit-2">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1l1-4l9.5-9.5Z" />
            </svg>
            Edit Profile
          </a>
        </div>
      @else
        <div class="flex flex-col gap-4">
          <a href="{{ route('messages.index') }}"
            class="text-caption text-primary flex flex-row gap-2 justify-center items-center border border-primary bg-primary/10 rounded-md px-4 py-2 hover:bg-primary hover:text-white w-max">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-send-icon lucide-send">
              <path
                d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z" />
              <path d="m21.854 2.147-10.94 10.939" />
            </svg>
            Kirim Pesan
          </a>
        </div>
      @endif
    </main>
  </section>
@endsection
