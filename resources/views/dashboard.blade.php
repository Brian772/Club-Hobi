@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="flex items-center justify-between h-max
  {{ $joinedClub->isNotEmpty() ? 'mb-6' : '' }}">
    <h1 class="text-title lg:text-heading-2 font-bold text-neutral-900">Halo, {{ auth()->user()->name }} </h1>
    @if ($joinedClub->isNotEmpty())
      <a href="{{ route('posts.create') }}"
        class="hidden lg:inline-flex items-center gap-2 bg-primary/10 text-primary hover:text-white text-sm font-semibold px-5 py-2.5 rounded-md hover:bg-primary">
        + Buat Postingan
      </a>
    @endif
  </div>
  @if ($joinedClub->isNotEmpty())
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-bold text-neutral-900">Club yang anda ikuti</h2>
      <a href="{{ route('clubs.index') }}" class="text-sm text-neutral-500 hover:text-neutral-800">Lihat selengkapnya
        →</a>
    </div>
  @endif
  <div
    class="
  {{ $joinedClub->isNotEmpty() ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8  pb-4 border-b border-hairline' : 'h-full w-full items-center justify-center' }}">
    @if ($joinedClub->isNotEmpty())
      @foreach ($joinedClub as $club)
        <a href="{{ route('clubs.show', $club->id) }}"
          class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
          <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="{{ $club->name }}"
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
      <section id="alreadyJoin" class="flex flex-col items-center justify-center w-full h-full">
        <div class="flex justify-center items-center w-full flex-col gap-1">
          <h2 class="text-ink text-title">Belum Ada Aktivitas</h2>
          <p class="text-caption text-ink-muted">Bergabung dengan klub untuk melihat postingan dan aktivitas terbaru
            di sini.</p>
        </div>
        <a href="{{ route('clubs.index') }}"
          class="text-primary mt-8 bg-primary/10 w-max px-4 py-2 rounded-md hover:text-white hover:bg-primary">Jelajahi
          Club</a>
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
                </div>
              </div>`;

        newCommentWrapper.querySelector('.reply-button').addEventListener('click', () => {
          replyComment(postId, commentData.id, userName);
        });
        newCommentWrapper.querySelector('.delete-button').addEventListener('click', () => {
          deleteComment(commentData.id, postId);
        });

        commentsContainer.appendChild(newCommentWrapper);
      }

      const scrollableArea = modal.querySelector('.overflow-y-auto');
      if (scrollableArea) {
        requestAnimationFrame(() => {
          scrollableArea.scrollTop = scrollableArea.scrollHeight;
        });
      }
    }

    function replyComment(postId, commentId, username) {
      const parentInput = document.getElementById(`parentId-${postId}`);
      const replyIndicator = document.getElementById(`replyIndicator-${postId}`);
      const replyTarget = document.getElementById(`replyTarget-${postId}`);
      const inputContent = document.getElementById(`inputContent-${postId}`);

      if (parentInput) parentInput.value = commentId;
      if (replyTarget) replyTarget.textContent = `@${username}`;
      if (replyIndicator) replyIndicator.classList.remove('hidden');
      if (inputContent) inputContent.focus();
    }

    function cancelReply(postId) {
      const parentInput = document.getElementById(`parentId-${postId}`);
      const replyIndicator = document.getElementById(`replyIndicator-${postId}`);

      if (parentInput) parentInput.value = '';
      if (replyIndicator) replyIndicator.classList.add('hidden');
    }

    async function deleteComment(commentId, postId) {
      if (!confirm('Hapus komentar ini?')) return;

      const modal = document.getElementById(`commentModal-${postId}`);
      if (!modal) {
        alert('Komentar tidak ditemukan.');
        return;
      }

      const commentElement = modal.querySelector(`[data-comment-id="${commentId}"]`);
      if (!commentElement) {
        alert('Komentar tidak ditemukan.');
        return;
      }

      if (commentElement.dataset.deleting === 'true') return;
      commentElement.dataset.deleting = 'true';

      const parent = commentElement.parentNode;
      const nextSibling = commentElement.nextSibling;
      const deletedCount = 1 + commentElement.querySelectorAll('[data-comment-id]').length;

      const commentsContainer = modal.querySelector('.overflow-y-auto .space-y-4');
      const emptyState = commentsContainer?.querySelector('p.text-center');

      if (emptyState) {
        emptyState.remove();
      }

      commentElement.remove();
      updateCommentsCount(postId, -deletedCount);

      if (
        commentsContainer &&
        !commentsContainer.querySelector('[data-comment-id]')
      ) {
        commentsContainer.insertAdjacentHTML(
          'beforeend',
          '<p class="text-xs text-neutral-400 text-center py-8" data-empty-comments="true">Belum ada komentar.</p>'
        );
      }

      try {
        const response = await fetch(`{{ url('/comments') }}/${encodeURIComponent(commentId)}`, {
          method: 'DELETE',
          credentials: 'same-origin',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const currentEmptyState = commentsContainer?.querySelector('[data-empty-comments="true"]');
        if (
          currentEmptyState &&
          commentsContainer.querySelector('[data-comment-id]')
        ) {
          currentEmptyState.remove();
        }
      } catch (error) {
        if (parent) {
          if (nextSibling && nextSibling.parentNode === parent) {
            parent.insertBefore(commentElement, nextSibling);
          } else {
            parent.appendChild(commentElement);
          }
        }

        commentElement.dataset.deleting = 'false';
        updateCommentsCount(postId, deletedCount);

        const currentEmptyState = commentsContainer?.querySelector('[data-empty-comments="true"]');
        if (currentEmptyState) {
          currentEmptyState.remove();
        }

        console.error('Delete Comment Error:', error);
        alert(error.message || 'Terjadi kesalahan saat menghapus komentar.');
      }
    }

    const slideIndices = {};

    function moveSlide(postId, direction) {
      if (!(postId in slideIndices)) slideIndices[postId] = 0;
      const slidesContainer = document.getElementById(`slides-${postId}`);
      const totalSlides = slidesContainer.children.length;

      slideIndices[postId] = (slideIndices[postId] + direction + totalSlides) % totalSlides;
      const currentIndex = slideIndices[postId];
      slidesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;

      const dots = document.querySelectorAll(`#dots-${postId} span`);
      dots.forEach((dot, idx) => {
        dot.classList.toggle('!bg-white', idx === currentIndex);
        dot.classList.toggle('w-2.5', idx === currentIndex);
      });
    }

    const modalSlideIndices = {};

    function moveModalSlide(postId, direction) {
      if (!(postId in modalSlideIndices)) modalSlideIndices[postId] = 0;
      const slidesContainer = document.getElementById(`modal-slides-${postId}`);
      const totalSlides = slidesContainer.children.length;

      modalSlideIndices[postId] = (modalSlideIndices[postId] + direction + totalSlides) % totalSlides;
      slidesContainer.style.transform = `translateX(-${modalSlideIndices[postId] * 100}%)`;
    }

    let currentModalMediaList = [];
    let currentModalIndex = 0;

    function openMediaModal(mediaList, index = 0) {
      currentModalMediaList = mediaList;
      currentModalIndex = index;
      const modal = document.getElementById('mediaModal');
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      renderModalContent();
    }

    async function downloadFileAsync(downloadUrl, filename, btnElement) {
      const originalText = btnElement.innerHTML;
      btnElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengunduh...';
      btnElement.style.pointerEvents = 'none';

      try {
        const response = await fetch(downloadUrl, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (response.status === 401 || response.status === 419) {
          alert('Sesi login telah berakhir. Silakan login kembali.');
          window.location.reload();
          return;
        }

        if (!response.ok) {
          throw new Error('Gagal mengunduh file.');
        }

        const blob = await response.blob();
        const blobUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = blobUrl;
        a.download = filename || 'download';
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(blobUrl);
      } catch (error) {
        console.error('Download error:', error);
        alert(error.message || 'Terjadi kesalahan saat mengunduh file.');
      } finally {
        btnElement.innerHTML = originalText;
        btnElement.style.pointerEvents = 'auto';
      }
    }

    function renderModalContent() {
      const content = document.getElementById('modalContent');
      const counter = document.getElementById('modalCounter');
      const prevBtn = document.getElementById('modalPrevBtn');
      const nextBtn = document.getElementById('modalNextBtn');

      if (!currentModalMediaList || currentModalMediaList.length === 0) return;

      const item = currentModalMediaList[currentModalIndex];
      const {
        url,
        download_url,
        ext,
        filename
      } = item;

      if (currentModalMediaList.length > 1) {
        prevBtn.classList.remove('hidden');
        nextBtn.classList.remove('hidden');
        counter.classList.remove('hidden');
        counter.textContent = `${currentModalIndex + 1} / ${currentModalMediaList.length}`;
      } else {
        prevBtn.classList.add('hidden');
        nextBtn.classList.add('hidden');
        counter.classList.add('hidden');
      }

      const downloadButtonHtml = `
            <button type="button" onclick="downloadFileAsync('${download_url}', '${filename}', this)" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow transition-colors cursor-pointer">
              <i class="fa-solid fa-download"></i> Unduh File
            </button>`;

      if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
        content.innerHTML = `
            <div class="relative flex flex-col items-center">
              <img src="${url}" class="max-w-full max-h-[75vh] object-contain rounded-lg shadow-lg">
              ${downloadButtonHtml}
            </div>`;
      } else if (['mp4', 'mov', 'webm'].includes(ext)) {
        const mimeType = ext === 'mov' ? 'video/quicktime' : `video/${ext}`;
        content.innerHTML = `
            <div class="relative flex flex-col items-center">
              <video controls autoplay class="max-w-full max-h-[75vh] object-contain rounded-lg shadow-lg">
                <source src="${url}" type="${mimeType}">
              </video>
              ${downloadButtonHtml}
            </div>`;
      } else if (['mp3', 'wav', 'ogg', 'm4a'].includes(ext)) {
        content.innerHTML = `
            <div class="bg-white p-6 rounded-2xl shadow-xl flex flex-col items-center gap-4 min-w-[320px] max-w-md w-full">
              <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                <i class="fa-solid fa-music text-2xl"></i>
              </div>
              <p class="text-sm font-semibold text-neutral-800 text-center truncate w-full">${filename}</p>
              <audio controls autoplay class="w-full"><source src="${url}" type="audio/${ext}"></audio>
              <button type="button" onclick="downloadFileAsync('${download_url}', '${filename}', this)" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg text-center transition-colors cursor-pointer flex items-center justify-center gap-2">
                <i class="fa-solid fa-download"></i> Unduh File
              </button>
            </div>`;
      } else {
        content.innerHTML = `
            <div class="bg-white rounded-xl overflow-hidden shadow-2xl w-full max-w-3xl h-[78vh] flex flex-col">
              <div class="flex items-center justify-between p-3.5 bg-neutral-100 border-b border-neutral-200">
                <span class="text-xs font-semibold text-neutral-700 truncate max-w-[70%]">${filename}</span>
                <button type="button" onclick="downloadFileAsync('${download_url}', '${filename}', this)" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors cursor-pointer flex items-center gap-2">
                  <i class="fa-solid fa-download"></i> Unduh File
                </button>
              </div>
              <iframe src="${url}" class="w-full h-full border-none"></iframe>
            </div>`;
      }
    }

    function changeModalSlide(direction) {
      if (currentModalMediaList.length <= 1) return;
      currentModalIndex = (currentModalIndex + direction + currentModalMediaList.length) % currentModalMediaList.length;
      renderModalContent();
    }

    function closeMediaModal() {
      const modal = document.getElementById('mediaModal');
      const content = document.getElementById('modalContent');
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      content.innerHTML = '';
      currentModalMediaList = [];
      currentModalIndex = 0;
    }

    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' || event.key === 'Esc') {
        closeMediaModal();
        document.querySelectorAll('[id^="commentModal-"]').forEach(m => closeCommentModal(m.id));
      } else if (event.key === 'ArrowLeft') {
        changeModalSlide(-1);
      } else if (event.key === 'ArrowRight') {
        changeModalSlide(1);
      }
    });
  </script>
@endsection
