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
  <header class="flex flex-col justify-center items-start md:items-stretch md:flex-row w-full pb-4">
    <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="Logo {{ $club->name }}"
      class="rounded-md w-full md:w-100 h-48 md:h-auto object-cover mb-4 md:mb-0 md:mr-4 border border-hairline">
    <div class="flex flex-col justify-between items-start w-full self-stretch">
      <div class="flex flex-col gap-2">
        <div class="flex flex-col gap-2 mb-2">
          <h1 class="text-heading-2 text-ink font-bold">Klub {{ $club->name }}</h1>
          <span class="text-caption lg:text-body-mid text-ink-muted">{{ $club->hobby->name }} <span
              class="font-extrabold">·</span>
            {{ $club->members_count }} anggota</span>
        </div>
        <p class="text-caption lg:text-body-mid text-ink-muted">{{ $club->description }}</p>
      </div>
      <div class="mt-2 flex flex-row gap-2 items-center justify-start w-full">
        <form action="{{ route('clubs.leave', $club->id) }}" method="POST">
          @csrf
          @method('DELETE')
          <x-secondary-button class="w-max px-4" type="submit">
            Keluar Klub
          </x-secondary-button>
        </form>

        @can('admin')
          <x-secondary-button class="w-max px-4" type="button"
            onclick="window.location='{{ route('admin.clubs.edit', $club->id) }}'">
            Edit Klub
          </x-secondary-button>
        @endcan
      </div>
    </div>
  </header>

  <section id="postingan">
    <main x-data="{ tab: 'post' }" class="flex flex-col gap-4">
      <div class="w-full h-max flex felx-row gap-6 border-b border-hairline">
        <button @click="tab = 'post'" :class="tab === 'post' ? 'text-primary border-b-2 border-primary' : 'text-ink'"
          class="w-max py-2 px-4 text-center flex flex-row justify-center cursor-pointer items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-text-align-start-icon lucide-text-align-start">
            <path d="M21 5H3" />
            <path d="M15 12H3" />
            <path d="M17 19H3" />
          </svg>
          Postingan
        </button>
        <button @click="tab = 'member'"
          :class="tab === 'member' ? 'text-primary border-b-2 border-primary' : 'text-ink'"
          class="w-max py-2 px-4 text-center flex flex-row justify-center cursor-pointer items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-users-round-icon lucide-users-round">
            <path d="M18 21a8 8 0 0 0-16 0" />
            <circle cx="10" cy="8" r="5" />
            <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
          </svg>
          Member
        </button>
      </div>

      {{-- Postingan --}}
      <div x-show="tab === 'post'">
        <header class="mb-4">
          <h2 class="text-heading-2 text-ink font-bold">Postingan</h2>
        </header>
        @if ($club->posts->isEmpty())
          <p class="text-caption text-ink-muted">Belum ada postingan di klub ini.</p>
        @else
          @foreach ($posts as $post)
            {{-- @if ($post->is_announcement)
              <x-post-announcement :post="$post" />
            @else
              <section class="block lg:hidden">
                <x-post-mobile :post="$post" />
              </section>
              <section class="hidden lg:block">
                <x-post :post="$post" />
              </section>
            @endif --}}
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
              {{-- Header Postingan --}}
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <img src="{{ $user->avatar_full_url }}" alt="Foto Profil" class="w-9 h-9 rounded-full object-cover"
                    alt="{{ $post->user->name ?? 'User' }}">
                  <div>
                    <span class="text-sm font-semibold text-neutral-900">{{ $post->user->name ?? 'Anonim' }}</span>
                    <span class="text-sm text-neutral-400">memposting di <span
                        class="font-medium text-neutral-700">{{ $post->club->name }}</span></span>
                  </div>
                </div>
                <span class="text-xs text-neutral-400">{{ $post->created_at->diffForHumans() }}</span>
              </div>

              {{-- Judul dan Isi Postingan --}}
              @if ($post->title)
                <h3 class="text-base font-bold text-neutral-900 mb-1">{{ $post->title }}</h3>
              @endif
              <p class="text-neutral-800 text-sm mb-3 leading-relaxed">{{ $post->content }}</p>

              {{-- Lampiran Media (Gambar / Video) --}}
              @if ($post->media_url)
                @php
                  $extension = strtolower(pathinfo($post->media_url, PATHINFO_EXTENSION));
                  $mediaUrl = asset('storage/' . $post->media_url);
                @endphp

                <div
                  class="mb-3 rounded-lg overflow-hidden max-h-[400px] bg-neutral-100 flex items-center justify-center cursor-pointer"
                  onclick="openMediaModal('{{ $mediaUrl }}', '{{ $extension }}')">
                  @if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']))
                    <img src="{{ $mediaUrl }}"
                      class="w-full h-[140px] object-cover rounded-lg hover:opacity-95 transition"
                      alt="{{ $post->title }}">
                  @elseif (in_array($extension, ['mp4', 'mov']))
                    <video class="w-full h-[400px] object-cover rounded-lg">
                      <source src="{{ $mediaUrl }}" type="video/{{ $extension }}">
                    </video>
                  @else
                    <div class="p-4 text-center">
                      <i class="fa-regular fa-file text-3xl text-neutral-500 mb-2"></i>
                      <p class="text-xs text-neutral-600 font-medium">Klik untuk membuka/mengunduh dokumen</p>
                    </div>
                  @endif
                </div>
              @endif

              {{-- Tombol Interaksi (Like & Comment) --}}
              <div class="flex items-center gap-6 pt-3 border-t border-neutral-100 text-neutral-500 text-xs font-medium">
                @php
                  $isLiked = $post->likes->contains('user_id', auth()->id());
                @endphp
                <form action="{{ route('posts.like', $post->id) }}" method="POST" class="inline">
                  @csrf
                  <button type="submit" class="flex items-center gap-1.5 hover:text-blue-600 transition-colors">
                    <i class="{{ $isLiked ? 'fa-solid' : 'fa-regular' }} fa-heart text-sm"></i>
                    <span>{{ $post->likes_count ?? 0 }} Suka</span>
                  </button>
                </form>

                <button type="button" onclick="toggleComments('comments-{{ $post->id }}')"
                  class="flex items-center gap-1.5 hover:text-blue-600 transition-colors">
                  <i class="fa-regular fa-comment text-sm"></i>
                  <span>{{ $post->comments_count ?? 0 }} Komentar</span>
                </button>
              </div>

              {{-- Section Komentar (Hidden by default) --}}
              <div id="comments-{{ $post->id }}" class="hidden pt-4 mt-3 border-t border-dashed border-neutral-200">
                <form action="{{ route('posts.comments.store', $post->id) }}" method="POST" class="flex gap-2 mb-3">
                  @csrf
                  <input type="text" name="content" placeholder="Tulis komentar..." required
                    class="flex-1 bg-neutral-50 border border-neutral-200 text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                  <button type="submit"
                    class="bg-blue-600 text-white text-xs px-3 py-2 rounded-lg font-semibold hover:bg-blue-700">
                    Kirim
                  </button>
                </form>

                <div class="space-y-2">
                  @forelse ($post->comments->whereNull('parent_id') as $comment)
                    @include('partials.comment-item', ['comment' => $comment, 'postId' => $post->id])
                  @empty
                    <p class="text-xs text-neutral-400">Belum ada komentar.</p>
                  @endforelse
                </div>
              </div>

              <div id="mediaModal" class="fixed inset-0 z-50 hidden bg-black/80 flex items-center justify-center p-4"
                onclick="closeMediaModal()">
                <div class="relative max-w-4xl w-full max-h-[90vh] flex items-center justify-center"
                  onclick="event.stopPropagation()">
                  <button onclick="closeMediaModal()"
                    class="absolute -top-10 right-0 text-white text-2xl font-bold hover:text-neutral-300">
                    &times;
                  </button>
                  <div id="modalContent" class="w-full flex justify-center"></div>
                </div>
              </div>

            </div>
          @endforeach
        @endif
      </div>

      {{-- Member --}}
      <div x-show="tab === 'member'">
        <header class="mb-4">
          <h2 class="text-heading-2 text-ink font-bold">Member</h2>
        </header>
        @if (empty($members))
          <p class="text-caption text-ink-muted">Belum ada member di klub ini.</p>
        @else
          <div class="flex flex-col gap-2">
            @foreach ($members as $member)
              <div
                class="flex flex-row w-full lg:px-4 py-2 items-center justify-between gap-4 mx-auto border border-canvas hover:border-hairline rounded-lg transition-colors duration-300">
                <div class="flex flex-row gap-2 items-center justify-start w-max">
                  <div class="min-w-10 mr-2">
                    <img src="{{ $member->user->avatar_full_url }}" alt="{{ $member->user->name }}"
                      class="rounded-full w-10 h-10 object-cover">
                  </div>
                  <div class="flex flex-col gap-2">
                    <h3 class="text-body-mid font-semibold text-ink">{{ $member->user->name }}
                      @if ($member->user->role_global === 'admin')
                        <span
                          class="text-overline text-primary bg-primary/10 rounded-full px-2 py-1 border border-primary font-semibold">Admin</span>
                      @endif
                    </h3>
                    <p class="text-caption text-ink-muted"><span class="text-ink font-semibold">Hobi:
                      </span>{{ implode(', ', $member->user->interest_array ?? []) }}</p>
                  </div>
                </div>

                <div class="flex flex-row gap-2 items-center w-max">
                  <span class="text-caption text-ink-muted">{{ $member->role }}</span>
                  @if ($member->user->id !== Auth::user()->id)
                    <div x-data="{ MenuOpen: false }">
                      <button type="button" @click="MenuOpen = true"
                        class="text-ink-muted text-body-mid rounded-full p-2 hover:bg-hairline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round" class="lucide lucide-ellipsis-vertical">
                          <circle cx="12" cy="12" r="1" />
                          <circle cx="12" cy="5" r="1" />
                          <circle cx="12" cy="19" r="1" />
                        </svg>
                      </button>

                      <div x-show="MenuOpen" x-cloak @keydown.escape.window="MenuOpen = false">
                        <div x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0 -translate-y-2"
                          x-transition:enter-end="opacity-100 translate-y-0"
                          x-transition:leave="transition ease-in duration-200"
                          x-transition:leave-start="opacity-100 translate-y-0"
                          x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="MenuOpen = false"
                          class="absolute z-50 right-5 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
                          <a href="{{ route('profile.show', ['user' => $member->user->id]) }}"
                            class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round" class="lucide lucide-user-round">
                              <circle cx="12" cy="8" r="5" />
                              <path d="M20 21a8 8 0 0 0-16 0" />
                            </svg>
                            Lihat Profil
                          </a>
                          <a href="{{ route('messages.index', ['conversation' => $member->user->id]) }}"
                            class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round" class="lucide lucide-message-circle-icon lucide-message-circle">
                              <path
                                d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719" />
                            </svg>
                            Kirim Pesan
                          </a>
                          @can('admin')
                            @if ($member->user->role_global !== 'admin')
                              <form action="{{ route('admin.clubs.kick', [$member->club_id, $member->user_id]) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                  class="w-full flex flex-row gap-2 items-center text-left px-4 py-2 text-caption text-red-500 rounded-md hover:bg-hairline">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-user-round-x-icon lucide-user-round-x">
                                    <path d="m16.5 16.5 5 5" />
                                    <path d="M2 21a8 8 0 0 1 11.531-7.18" />
                                    <path d="m21.5 16.5-5 5" />
                                    <circle cx="10" cy="8" r="5" />
                                  </svg>
                                  Kick Member
                                </button>
                              </form>
                            @endif
                          @endcan
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </main>
  </section>

  <script>
    function toggleComments(id) {
      const el = document.getElementById(id);
      if (el) {
        el.classList.toggle('hidden');
      }
    }

    function replyComment(postId, commentId, username) {
      const input = document.querySelector(`#comments-${postId} input[name="content"]`);
      let parentInput = document.querySelector(`#comments-${postId} input[name="parent_id"]`);

      if (!parentInput) {
        parentInput = document.createElement('input');
        parentInput.type = 'hidden';
        parentInput.name = 'parent_id';
        input.parentNode.appendChild(parentInput);
      }

      parentInput.value = commentId;
      input.placeholder = `Membalas ${username}...`;
      input.focus();
    }

    function openMediaModal(url, ext) {
      const modal = document.getElementById('mediaModal');
      const content = document.getElementById('modalContent');
      const imageExtensions = ['jpg', 'jpeg', 'png', 'webp'];
      const videoExtensions = ['mp4', 'mov'];

      if (imageExtensions.includes(ext)) {
        content.innerHTML = `<img src="${url}" class="max-w-full max-h-[85vh] rounded-lg object-contain">`;
      } else if (videoExtensions.includes(ext)) {
        content.innerHTML =
          `<video controls autoplay class="max-w-full max-h-[85vh] rounded-lg"><source src="${url}" type="video/${ext}"></video>`;
      } else {
        window.open(url, '_blank');
        return;
      }

      modal.classList.remove('hidden');
    }

    function closeMediaModal() {
      const modal = document.getElementById('mediaModal');
      const content = document.getElementById('modalContent');
      modal.classList.add('hidden');
      content.innerHTML = '';
    }
  </script>
@endsection
