@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <header class="flex flex-row gap-2 lg:gap-4 items-center justify-start mb-3">
    <a href="{{ route('clubs.index', $club->id) }}" class="text-ink-muted">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
        <path d="M0 0h16v9H0z" fill="none" />
        <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
        <path fill="currentColor"
          d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
      </svg>
    </a>
    <h2 class="text-title lg:text-heading-2 flex flex-row items-center gap-2 justify-center text-ink">{{ $club->name }}
    </h2>
  </header>
  <div x-data="{ OpenLeaveModal: false }" class="flex flex-col justify-center items-start md:items-stretch md:flex-row w-full pb-4">
    <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="Logo {{ $club->name }}"
      class="rounded-md w-full md:w-100 h-48 md:h-64 object-cover mb-4 md:mb-0 md:mr-4 border border-hairline">
    <div class="flex flex-col justify-between items-start w-full self-stretch">
      <div class="flex flex-col gap-2">
        <div class="flex flex-col mb-2">
          <span class="text-caption lg:text-body-mid text-ink-muted">{{ $club->hobby->name }} <span
              class="font-extrabold">·</span>
            {{ $club->members_count }} Anggota</span>
          <p class="text-caption text-ink-muted">Owner : {{ $creator->user->name ?? 'Tidak Diketahui' }}</p>
        </div>
        <p class="text-caption lg:text-body-mid text-ink-muted">{{ $club->description }}</p>
      </div>
      <?php
      use App\Models\ClubMember;
      
      $isOwnerOrModerator = ClubMember::where('club_id', $club->id)
          ->where('user_id', auth()->user()->id)
          ->whereIn('role', ['owner', 'moderator'])
          ->first();
      ?>
      <div
        class="mt-2 flex flex-row gap-4 items-center w-full justify-start
        {{ $isOwnerOrModerator ? 'lg:justify-between' : 'lg:justify-end' }}">
        @can('view', $club)
          <a href="{{ route('clubs.settings', $club->id) }}"
            class="order-2 lg:order-1 rounded-md text-body-mid text-ink hover:bg-primary/10 hover:text-primary py-2 px-4 flex flex-row gap-2 items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-settings-icon lucide-settings">
              <path
                d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915" />
              <circle cx="12" cy="12" r="3" />
            </svg>
            Settings
          </a>
        @endcan
        @cannot('isOwner', $club)
            <button type="button" @click="OpenLeaveModal = true"
              class="lg:text-ink-muted text-accent-red bg-accent-red/10 rounded-md border border-accent-red lg:bg-canvas lg:border-none  text-body-mid px-4 py-2 cursor-pointer lg:hover:text-accent-red lg:hover:underline lg:hover:underline-offset-2">
              Keluar
            </button>
        @endcannot
      </div>

    </div>
    <div x-show="OpenLeaveModal" x-cloak @keydown.escape.window="OpenLeaveModal = false"
      class="fixed flex items-center justify-center inset-0 z-50">
      <div @click="OpenLeaveModal = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
      </div>
      <div class="fixed p-6 h-max rounded-lg z-50 w-max bg-canvas border border-hairline overflow-hidden max-w-md">
        <div class="flex flex-col gap-2 mb-4">
          <h3 class="text-title mb-4 text-ink">
            Leave <span x-text="selectedName"></span>?
          </h3>
          <p class="text-body-mid mb-4 text-ink">Apakah Anda yakin ingin meninggalkan klub ini? Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="flex flex-row justify-end gap-2">
          <form action="{{ route('clubs.leave', $club->id) }}" method="POST" class="order-1 lg:order-2">
            @csrf
            @method('DELETE')
            <button type="submit"
              class="px-4 py-2 text-caption bg-accent-red/10 rounded w-max text-accent-red hover:text-white hover:bg-accent-red">
              Leave
            </button>
          </form>
          <button type="button"
            class="bg-gray-200 border border-hairline rounded text-caption px-4 py-2 text-ink hover:bg-gray-300"
            @click="OpenLeaveModal = false">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <section id="postingan" x-data="{
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
        @if ($club->posts->isNotEmpty())
          <header class="mb-4 flex flex-row items-center justify-between">
            <h2 class="text-title lg:text-heading-2 text-ink font-bold">Postingan</h2>
            <a href="{{ route('posts.create') }}"
              class="inline-flex items-center gap-2 bg-primary/10 text-primary hover:text-white text-sm font-semibold px-5 py-2.5 rounded-md hover:bg-primary">
              + Buat Postingan
            </a>
          </header>
        @endif
        @if ($club->posts->isEmpty())
          <div class="flex flex-col items-center justify-center h-full">
            <p class="text-body-mid mt-12 text-ink-muted">Belum ada postingan di klub ini.</p>
            <a href="{{ route('posts.create') }}"
              class="inline-flex items-center mt-8 bg-primary/10 text-primary hover:text-white text-sm font-semibold px-5 py-2.5 rounded-md hover:bg-primary">
              + Buat Postingan
            </a>
          </div>
        @else
          <div class="max-w-100 space-y-4">
            @foreach ($posts as $post)
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
        @endif
      </div>

      {{-- Member --}}
      <div x-show="tab === 'member'">
        <header class="mb-4">
          <h2 class="text-title lg:text-heading-2 text-ink font-bold">Member</h2>
        </header>
        @if (empty($members))
          <p class="text-caption text-ink-muted">Belum ada member di klub ini.</p>
        @else
          <div class="flex flex-col gap-2">
            @foreach ($members as $member)
              <x-member-list :member="$member" />
            @endforeach
          </div>
        @endif
      </div>
    </main>
    <x-report-modal />
  </section>

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
