@extends('layouts.app')

@section('title', 'Orbii | Home')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div x-data="{
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
    <div class="flex items-center justify-between h-max
  {{ $joinedClub->isNotEmpty() ? 'mb-6' : '' }}">
      <h1 class="text-title lg:text-3xl font-semibold text-neutral-900">Halo, {{ auth()->user()->name }} </h1>
      @if ($joinedClub->isNotEmpty())
        <a href="{{ route('posts.create') }}"
          class="hidden lg:inline-flex items-center gap-2 bg-primary/10 text-primary hover:text-white text-sm font-semibold px-5 py-2.5 rounded-md hover:bg-primary">
          + Make a post
        </a>
      @endif
    </div>
    @if ($joinedClub->isNotEmpty())
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-neutral-900">Club yang anda ikuti</h2>
        <a href="{{ route('clubs.index') }}" class="text-sm text-neutral-500 hover:text-neutral-800">Lihat selengkapnya
          →</a>
      </div>
    @endif
    <div
      class="
  {{ $joinedClub->isNotEmpty() ? 'overflow-hidden h-max gap-6 mb-8  pb-4 border-b border-hairline' : 'h-full w-full items-center justify-center' }}">
      @if ($joinedClub->isNotEmpty())
        <div class="flex flex-row gap-6 overflow-x-auto scrollbar-hide h-max pb-4">
          @foreach ($joinedClub as $club)
            <a href="{{ route('clubs.show', $club->id) }}"
              class="bg-white rounded-3xl border border-hairline overflow-hidden items-stretch min-w-75 w-100 hover:shadow-lg transition-shadow duration-300 flex flex-col">
              <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="{{ $club->name }}"
                class="w-full h-48 object-cover">
              <div class="p-4 flex flex-col flex-1">
                <h3 class="text-body-mid font-semibold mb-2 flex flex-row items-center justify-between">
                  {{ $club->name }}
                  <span class="text-caption text-ink-muted">{{ $club->hobby->name ?? 'Kategori Tidak Diketahui' }}</span>
                </h3>
                <p class="text-caption text-ink-secondary mb-2 line-clamp-2">{{ $club->description }}</p>
                <div class="flex flex-row items-center justify-between mt-2">
                  <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
                  <p class="text-caption text-ink-muted flex flex-row gap-2 items-center">
                    @if ($club->visibility === 'public')
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-globe preview-icon">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                        <path d="M2 12h20" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-lock preview-icon">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                      </svg>
                    @endif
                    {{ $club->visibility }}
                  </p>
                </div>
              </div>
            </a>
          @endforeach
        </div>
      @else
        <section id="alreadyJoin" class="flex pt-24 flex-col items-center justify-center w-full h-full">
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

    <div class="max-w-150 space-y-4">
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

    <x-report-modal />
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

    const likeInFlight = {};

    async function toggleLike(postId) {
      if (likeInFlight[postId]) return; // cegah klik ganda / race condition
      likeInFlight[postId] = true;

      const feedBtn = document.getElementById(`likeBtn-${postId}`);
      const feedIcon = document.getElementById(`likeIcon-${postId}`);
      const feedCount = document.getElementById(`likeCount-${postId}`);
      const modalIcon = document.getElementById(`likeIconModal-${postId}`);
      const modalCount = document.getElementById(`likeCountModal-${postId}`);

      const wasLiked = feedBtn?.dataset.liked === 'true';
      const currentCount = parseInt(feedCount?.innerText) || 0;

      const applyLikeState = (liked, count) => {
        if (feedBtn) feedBtn.dataset.liked = liked ? 'true' : 'false';
        [feedIcon, modalIcon].forEach(icon => {
          if (!icon) return;
          icon.classList.toggle('fa-solid', liked);
          icon.classList.toggle('fa-regular', !liked);
          icon.classList.toggle('text-red-500', liked);
        });
        if (feedCount) feedCount.innerText = count;
        if (modalCount) modalCount.innerText = count;
      };

      applyLikeState(!wasLiked, currentCount + (wasLiked ? -1 : 1)); // optimistic UI

      try {
        const response = await fetch(feedBtn.dataset.likeUrl, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (!response.ok) throw new Error('Gagal memproses like.');

        const data = await response.json();
        // Sesuaikan key ini dengan response controller like kamu yang sebenarnya,
        // mis. { liked: true, likes_count: 12 }
        applyLikeState(data.liked, data.likes_count);

      } catch (error) {
        console.error('Like Error:', error);
        applyLikeState(wasLiked, currentCount); // rollback kalau request gagal
        alert('Gagal memproses like, coba lagi.');
      } finally {
        likeInFlight[postId] = false;
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
        let parentCommentElement = modal.querySelector(`[data-comment-id="${commentData.parent_id}"]`);

        // Balasan bisa ditujukan ke balasan lain (nested reply), bukan cuma ke komentar utama.
        // Elemen reply (class "flex gap-2.5") BUKAN wrapper thread, jadi kalau di-append
        // langsung ke situ akan merusak layout flex-nya. Naikkan ke wrapper thread utama
        // (class "space-y-2") supaya semua balasan tetap masuk ke .replies-container yang sama,
        // persis seperti hasil flatten $getReplies() di server saat halaman di-refresh.
        if (parentCommentElement && !parentCommentElement.classList.contains('space-y-2')) {
          parentCommentElement = parentCommentElement.closest('[data-comment-id].space-y-2');
        }

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
                <img src="${userAvatar}" class="size-8 rounded-full object-cover shrink-0 border border-neutral-200" alt="User">
                <div class="flex-1 min-w-0">
                  <p class="text-caption text-ink-secondary leading-snug break-words">
                    <span class="font-semibold text-neutral-900 mr-1.5">${escapeHtml(userName)}</span>
                    <span>${escapeHtml(commentData.content)}</span>
                  </p>
                  <div class="flex items-center gap-3 mt-1 text-[10px] text-ink-muted font-medium">
                    <span>Baru saja</span>
                    <button type="button" class="reply-button hover:text-blue-600 cursor-pointer">Balas</button>
                    <button type="button" class="delete-button hover:text-red-500 cursor-pointer">Hapus</button>
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
                <img src="${userAvatar}" class="size-8 rounded-full object-cover shrink-0 border border-neutral-200" alt="User">
                <div class="flex-1 min-w-0">
                  <p class="text-caption text-ink-secondary leading-snug break-words">
                    <span class="font-semibold text-neutral-900 mr-1.5">${escapeHtml(userName)}</span>
                    <span>${escapeHtml(commentData.content)}</span>
                  </p>
                  <div class="flex items-center gap-3 mt-1 text-[10px] text-ink-muted font-medium">
                    <span>Baru saja</span>
                    <button type="button" class="reply-button hover:text-blue-600 cursor-pointer">Balas</button>
                    <button type="button" class="delete-button hover:text-red-500 cursor-pointer">Hapus</button>
                  </div>
                </div>

                @if (!empty($joinedClub) && $joinedClub->isNotEmpty())
                    <div class="space-y-4">
                        @foreach ($joinedClub as $club)
                            <a href="{{ route('clubs.show', $club->id) }}" class="block rounded-xl border border-slate-200 p-3 transition hover:border-sky-200 hover:bg-sky-50">
                                <div class="flex items-start gap-3">
                                    <img src="{{ $club->cover_display_url ?? asset('images/default-club.png') }}" alt="{{ $club->name }}" class="h-16 w-16 rounded-lg object-cover">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $club->hobby->name ?? 'Kategori' }}</p>
                                        <h3 class="mt-1 text-base font-semibold text-slate-900">{{ $club->name }}</h3>
                                        <p class="mt-1 text-sm text-slate-600 line-clamp-2">{{ $club->description }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">Anda belum bergabung ke klub manapun.</p>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">Aktivitas terbaru</h2>

                @if (!empty($feedPosts) && $feedPosts->isNotEmpty())
                    <div class="mt-4 space-y-4">
                        @foreach ($feedPosts->take(4) as $post)
                            <div class="rounded-xl border border-slate-200 p-3">
                                <p class="text-sm font-semibold text-slate-900">{{ $post->user->name ?? 'Pengguna' }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ Str::limit($post->content ?? 'Tidak ada konten', 120) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">Belum ada aktivitas terbaru.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
