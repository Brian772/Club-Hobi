@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-neutral-900">Halo, {{ auth()->user()->name }} </h1>
    <a href="{{ route('posts.create') }}"
      class="hidden lg:inline-flex items-center gap-2 bg-blue-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-blue-700">
      + Buat Postingan
    </a>
  </div>

  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-neutral-900">Club yang anda ikuti</h2>
    <a href="{{ route('clubs.index') }}" class="text-sm text-neutral-500 hover:text-neutral-800">Lihat selengkapnya →</a>
  </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    @if ($joinedClub->isNotEmpty())
      @foreach ($joinedClub as $club)
        <a href="{{ route('clubs.show', $club->id) }}" class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
          <img src="{{ $club->cover_url }}" alt="{{ $club->name }}" class="w-full h-32 object-cover">
          <div class="p-4">
            <span class="text-caption text-ink-muted">{{ $club->category ?? 'Kategori Tidak Diketahui' }}</span>
            <h3 class="text-lg font-semibold mb-2">{{ $club->name }}</h3>
            <p class="text-caption text-ink-muted mb-2 line-clamp-2">{{ $club->description }}</p>
            <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
          </div>
        </a>
      @endforeach
    @else
      <section id="alreadyJoin" class="border-b border-hairline flex w-full">
        <div class="mb-12 flex justify-center w-full">
          <p class="text-caption text-ink-muted">Anda belum bergabung ke klub manapun.</p>
          <p></p>
        </div>
      </section>
    @endif
  </div>

  <div class="space-y-4">
    @foreach ($feedPosts as $post)
      <div class="bg-white rounded-xl border border-neutral-200 p-5">
        {{-- Header Postingan --}}
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-3">
            <img src="{{ $user->avatar_full_url}}" alt="Foto Profil" class="w-9 h-9 rounded-full object-cover"
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
              <img src="{{ $mediaUrl }}" class="w-full h-[140px] object-cover rounded-lg hover:opacity-95 transition"
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
  </div>

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
        content.innerHTML = `<video controls autoplay class="max-w-full max-h-[85vh] rounded-lg"><source src="${url}" type="video/${ext}"></video>`;
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