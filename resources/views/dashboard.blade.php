<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

<<<<<<< Updated upstream
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
=======
@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-title lg:text-heading-2 font-bold text-neutral-900">Halo, {{ auth()->user()?->name ?? 'Pengguna' }} </h1>
    <a href="{{ route('posts.create') }}"
      class="hidden lg:inline-flex items-center gap-2 bg-primary/10 text-primary hover:text-white text-sm font-semibold px-5 py-2.5 rounded-md hover:bg-primary">
      + Buat Postingan
    </a>
  </div>

  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-neutral-900">Club yang anda ikuti</h2>
    <a href="{{ route('clubs.index') }}" class="text-sm text-neutral-500 hover:text-neutral-800">Lihat selengkapnya →</a>
  </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8 border-b border-hairline pb-4">
    @if ($joinedClub->isNotEmpty())
      @foreach ($joinedClub as $club)
        <a href="{{ route('clubs.show', $club->id) }}"
          class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
          <img src="{{ $club->cover_display_url }}" alt="{{ $club->name }}"
            class="w-full h-48 object-cover">
          <div class="p-4">
            <span class="text-caption text-ink-muted">{{ $club->hobby->name ?? 'Kategori Tidak Diketahui' }}</span>
            <h3 class="text-lg font-semibold mb-2">{{ $club->name }}</h3>
            <p class="text-caption text-ink-muted mb-2 line-clamp-2">{{ $club->description }}</p>
            <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
          </div>
        </a>
      @endforeach
    @else
      <section id="alreadyJoin" class="flex w-full">
        <div class="mb-12 flex justify-center w-full">
          <p class="text-caption text-ink-muted">Anda belum bergabung ke klub manapun.</p>
        </div>
      </section>
    @endif
  </div>

  <div class="max-w-100 space-y-4">
    @foreach ($feedPosts as $post)
      <x-post :post="$post" />
    @endforeach

    <div id="mediaModal" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center p-4"
      onclick="closeMediaModal()">
      <button onclick="closeMediaModal()"
        class="absolute top-4 right-4 text-white text-3xl font-bold z-50 cursor-pointer">&times;</button>

      <button id="modalPrevBtn" onclick="event.stopPropagation(); changeModalSlide(-1)"
        class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 hover:bg-white/40 text-white rounded-full flex items-center justify-center text-xl z-50 cursor-pointer">‹</button>

      <button id="modalNextBtn" onclick="event.stopPropagation(); changeModalSlide(1)"
        class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 hover:bg-white/40 text-white rounded-full flex items-center justify-center text-xl z-50 cursor-pointer">›</button>

      <div class="relative max-w-4xl w-full max-h-[90vh] flex flex-col items-center justify-center"
        onclick="event.stopPropagation()">
        <div id="modalCounter" class="absolute -top-8 text-white/80 text-xs font-semibold"></div>
        <div id="modalContent" class="w-full flex flex-col items-center justify-center"></div>
      </div>
    </div>
  </div>

  <script>
    function openCommentModal(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
    }

    function closeCommentModal(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.querySelectorAll('video, audio').forEach(media => media.pause());
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
      }
    }

    async function submitComment(event, postId) {
      event.preventDefault();

      const form = document.getElementById(`commentForm-${postId}`);
      const inputContent = document.getElementById(`inputContent-${postId}`);
      const submitBtn = form.querySelector('button[type="submit"]');

      if (!inputContent.value.trim()) return;

      submitBtn.disabled = true;
      submitBtn.innerText = 'Mengirim...';

      try {
        const response = await fetch(`/posts/${postId}/comments`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: new FormData(form)
        });

        if (!response.ok) {
          let errorMsg = 'Gagal mengirim komentar.';
          try {
            const errData = await response.json();
            errorMsg = errData.message || errorMsg;
          } catch (e) {}
          throw new Error(errorMsg);
        }

        const data = await response.json();
        appendNewComment(postId, data);
        updateCommentsCount(postId, 1);
        inputContent.value = '';
        cancelReply(postId);

      } catch (error) {
        console.error('Comment Error:', error);
        alert(error.message || 'Terjadi kesalahan saat mengirim komentar.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Kirim';
      }
    }

    function updateCommentsCount(postId, diff) {
      const commentsCount = document.getElementById(`commentsCount-${postId}`);
      if (commentsCount) {
        const currentCount = parseInt(commentsCount.innerText) || 0;
        commentsCount.innerText = Math.max(0, currentCount + diff);
      }
    }

    function appendNewComment(postId, commentData) {
      const modal = document.getElementById(`commentModal-${postId}`);
      if (!modal) return;

      const commentsContainer = modal.querySelector('.overflow-y-auto .space-y-4');
      if (!commentsContainer) return;

      const emptyText = commentsContainer.querySelector('p.text-center');
      if (emptyText) emptyText.remove();

      const userName = commentData.user?.name || 'User';
      const userAvatar = commentData.user?.avatar_full_url || 'https://via.placeholder.com/32';

      const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      };

      if (commentData.parent_id) {
        const parentCommentElement = modal.querySelector(`[data-comment-id="${commentData.parent_id}"]`);
        if (parentCommentElement) {
          let repliesContainer = parentCommentElement.querySelector('.replies-container');
          if (!repliesContainer) {
            repliesContainer = document.createElement('div');
            repliesContainer.className = 'ml-9 space-y-2.5 border-l-2 border-neutral-100 pl-3 replies-container mt-2';
            parentCommentElement.appendChild(repliesContainer);
          }

          const replyWrapper = document.createElement('div');
          replyWrapper.className = 'flex gap-2.5';
          replyWrapper.setAttribute('data-comment-id', commentData.id);
          replyWrapper.innerHTML = `
                <img src="${userAvatar}" class="w-6 h-6 rounded-full object-cover shrink-0 border border-neutral-200" alt="User">
                <div class="flex-1 min-w-0">
                  <p class="text-xs text-neutral-800 leading-snug break-words">
                    <span class="font-bold text-neutral-900 mr-1.5">${escapeHtml(userName)}</span>
                    <span>${escapeHtml(commentData.content)}</span>
                  </p>
                  <div class="flex items-center gap-3 mt-1 text-[10px] text-neutral-400 font-medium">
                    <span>Baru saja</span>
                    <button type="button" class="reply-button hover:text-blue-600 cursor-pointer font-semibold">Balas</button>
                    <button type="button" class="delete-button hover:text-red-500 cursor-pointer font-semibold">Hapus</button>
                  </div>
                </div>`;

          replyWrapper.querySelector('.reply-button').addEventListener('click', () => {
            replyComment(postId, commentData.id, userName);
          });
          replyWrapper.querySelector('.delete-button').addEventListener('click', () => {
            deleteComment(commentData.id, postId);
          });

          repliesContainer.appendChild(replyWrapper);
        }
      } else {
        const newCommentWrapper = document.createElement('div');
        newCommentWrapper.className = 'space-y-2';
        newCommentWrapper.setAttribute('data-comment-id', commentData.id);
        newCommentWrapper.innerHTML = `
              <div class="flex gap-3">
                <img src="${userAvatar}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-neutral-200" alt="User">
                <div class="flex-1 min-w-0">
                  <p class="text-xs text-neutral-800 leading-snug break-words">
                    <span class="font-bold text-neutral-900 mr-1.5">${escapeHtml(userName)}</span>
                    <span>${escapeHtml(commentData.content)}</span>
                  </p>
                  <div class="flex items-center gap-3 mt-1 text-[10px] text-neutral-400 font-medium">
                    <span>Baru saja</span>
                    <button type="button" class="reply-button hover:text-blue-600 cursor-pointer font-semibold">Balas</button>
                    <button type="button" class="delete-button hover:text-red-500 cursor-pointer font-semibold">Hapus</button>
                  </div>
>>>>>>> Stashed changes
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
