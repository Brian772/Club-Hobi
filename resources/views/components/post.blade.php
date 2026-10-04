<div class="bg-canvas rounded-lg border border-hairline p-4 overflow-hidden">

  <div
    class="flex flex-row items-center
  {{ Auth::id() !== ($post->author->id ?? $post->user->id) ? 'justify-between' : 'justify-start' }}">
    <div class="flex items-center gap-3">
      @php
        $postAuthor = $post->author ?? $post->user;
      @endphp

      @if ($postAuthor->avatar_full_url)
        <img src="{{ $postAuthor->avatar_full_url }}" alt="{{ $postAuthor->name }}"
          class="rounded-full size-10 object-cover"
          onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
        <div
          class="hidden size-10 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
          {{ Str::upper(Str::substr($postAuthor->name, 0, 1)) }}
        </div>
      @else
        <div
          class="size-10 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
          {{ Str::upper(Str::substr($postAuthor->name, 0, 1)) }}
        </div>
      @endif
      <div class="flex flex-col gap-0.5">
        <span
          class="text-body-mid font-semibold text-neutral-900 block leading-tight">{{ $postAuthor->name ?? 'Anonim' }}</span>
        <span class="text-caption text-ink-muted">{{ $post->created_at->diffForHumans() }} •
          <span class="font-medium text-ink-muted">{{ $post->club->name ?? 'Umum' }}</span></span>
      </div>
    </div>
    @if (Auth::id() !== ($post->author->id ?? $post->user->id))
      <div x-data="{ MenuOpen: false }" class="relative">
        <button type="button" x-ref="button" @click="MenuOpen = true"
          class="text-ink-muted text-body-mid rounded-full p-2 hover:bg-hairline">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-ellipsis-vertical">
            <circle cx="12" cy="12" r="1" />
            <circle cx="12" cy="5" r="1" />
            <circle cx="12" cy="19" r="1" />
          </svg>
        </button>

        <div x-show="MenuOpen" x-cloak @keydown.escape.window="MenuOpen = false">
          <div x-anchor.noflip="$refs.button" x-transition.opacity @click.outside="MenuOpen = false"
            @click="MenuOpen = false"
            class="z-50 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
            <button type="button"
              @click="
              openReport(
                'post',
                '{{ $post->id }}',
                '{{ $postAuthor->id }}',
                '{{ route('reports.store') }}',
                @js([
    'id' => $post->id,
    'title' => $post->title,
    'content' => $post->content,
    'author_name' => $postAuthor->name ?? 'Anonim',
    'author_id' => $postAuthor->id,
])
              )"
              class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">

              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-flag preview-icon">
                <path
                  d="M4 22V4a1 1 0 0 1 .4-.8A6 6 0 0 1 8 2c3 0 5 2 7.333 2q2 0 3.067-.8A1 1 0 0 1 20 4v10a1 1 0 0 1-.4.8A6 6 0 0 1 16 16c-3 0-5-2-8-2a6 6 0 0 0-4 1.528" />
              </svg>
              Laporkan
            </button>
          </div>
        </div>
      </div>
    @endif
  </div>
  <div class="flex flex-col my-4">
    @if ($post->title)
      <h3 class="text-body-mid font-semibold text-ink mb-1">{{ $post->title }}</h3>
    @endif
    <p class="text-ink-secondary text-caption leading-relaxed">{{ $post->content }}</p>
  </div>

  @php
    $mediaCollection = collect();

    if ($post->relationLoaded('media') && $post->media->isNotEmpty()) {
        $mediaCollection = $post->media;
    } elseif (!empty($post->file_path)) {
        $mediaCollection = collect([(object) ['file_path' => $post->file_path, 'id' => null]]);
    } elseif (!empty($post->media_url)) {
        $mediaCollection = collect([(object) ['file_path' => $post->media_url, 'id' => null]]);
    }

    $mediaList = $mediaCollection
        ->map(function ($item) {
            $filePath = is_object($item) ? $item->file_path ?? ($item->url ?? '') : $item;
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $filename = basename($filePath);
            $mediaId = is_object($item) ? $item->id ?? null : null;

            $mediaUrl = Str::startsWith($filePath, ['http://', 'https://']) ? $filePath : asset('storage/' . $filePath);

            $downloadUrl = $mediaId ? route('media.download', $mediaId) : $mediaUrl;

            return [
                'id' => $mediaId,
                'url' => $mediaUrl,
                'download_url' => $downloadUrl,
                'ext' => $extension,
                'filename' => $filename,
            ];
        })
        ->values();
  @endphp

  @if ($mediaCollection->isNotEmpty())
    <div class="relative group w-full overflow-hidden rounded-none -mt-1 my-2" id="carousel-{{ $post->id }}">
      <div class="flex transition-transform duration-300 ease-in-out" id="slides-{{ $post->id }}">
        @foreach ($mediaList as $index => $item)
          @php
            $isImage = in_array($item['ext'], ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            $isVideo = in_array($item['ext'], ['mp4', 'mov', 'webm']);
            $isAudio = in_array($item['ext'], ['mp3', 'wav', 'ogg', 'm4a']);
          @endphp

          <div class="w-full shrink-0">
            @if ($isImage)
              <div class="cursor-pointer flex items-start justify-start bg-neutral-100 rounded-xl overflow-hidden"
                onclick='openMediaModal({{ json_encode($mediaList) }}, {{ $index }})'>
                <img src="{{ $item['url'] }}" class="w-full h-100 object-contain pointer-events-none rounded-lg"
                  alt="Post Media">
              </div>
            @elseif ($isVideo)
              <div class="flex items-center justify-center cursor-pointer bg-neutral-100 rounded-xl overflow-hidden"
                onclick='openMediaModal({{ json_encode($mediaList) }}, {{ $index }})'>
                <video class="w-max h-100 object-contain pointer-events-none" controls preload="metadata">
                  <source src="{{ $item['url'] }}"
                    type="video/{{ $item['ext'] === 'mov' ? 'quicktime' : $item['ext'] }}">
                </video>
              </div>
            @else
              <div class="p-4 bg-white">
                <div onclick='openMediaModal({{ json_encode($mediaList) }}, {{ $index }})'
                  class="flex items-center gap-3 p-3 bg-neutral-50 hover:bg-neutral-100 border border-neutral-200 rounded-lg cursor-pointer transition-colors">
                  @if ($isAudio)
                    <div class="size-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                      <i class="fa-solid fa-music text-lg"></i>
                    </div>
                  @else
                    <div
                      class="size-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                      <i class="fa-solid fa-file-lines text-lg"></i>
                    </div>
                  @endif
                  <div class="flex-1 min-w-0">
                    <p class="text-small font-semibold text-neutral-800 truncate">{{ $item['filename'] }}</p>
                    <span class="text-caption uppercase font-semibold text-neutral-400">{{ $item['ext'] }} File</span>
                  </div>
                  <i class="fa-solid fa-expand text-small text-neutral-400"></i>
                </div>
              </div>
            @endif
          </div>
        @endforeach
      </div>

      @if ($mediaList->count() > 1)
        <button type="button" onclick="moveSlide('{{ $post->id }}', -1)"
          class="absolute left-2 top-1/2 -translate-y-1/2 w-7 h-7 bg-black/50 text-white rounded-full flex items-center justify-center text-small opacity-0 group-hover:opacity-100 transition-opacity z-10 cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-chevron-left preview-icon">
            <path d="m15 18-6-6 6-6" />
          </svg>
        </button>
        <button type="button" onclick="moveSlide('{{ $post->id }}', 1)"
          class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 bg-black/50 text-white rounded-full flex items-center justify-center text-small opacity-0 group-hover:opacity-100 transition-opacity z-10 cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-chevron-right preview-icon">
            <path d="m9 18 6-6-6-6" />
          </svg>
        </button>

        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex items-center gap-1.5 z-10"
          id="dots-{{ $post->id }}">
          @foreach ($mediaList as $idx => $item)
            <span
              class="w-1.5 h-1.5 rounded-full bg-white/50 transition-all {{ $idx === 0 ? 'bg-white! w-2.5' : '' }}"></span>
          @endforeach
        </div>
      @endif
    </div>
  @endif

  <div class="">
    <div class="flex items-center gap-6 text-neutral-600 text-small font-medium">
      @php
        $isLiked = $post->likes->contains('user_id', auth()->id());
      @endphp
      <button type="button" id="likeBtn-{{ $post->id }}" onclick="toggleLike('{{ $post->id }}')"
        data-liked="{{ $isLiked ? 'true' : 'false' }}" data-like-url="{{ route('posts.like', $post->id) }}"
        class="flex items-center gap-1 hover:text-red-500 transition-colors">
        <i id="likeIcon-{{ $post->id }}"
          class="{{ $isLiked ? 'fa-solid text-red-500' : 'fa-regular' }} fa-heart text-base"></i>
        <span><span id="likeCount-{{ $post->id }}">{{ $post->likes_count ?? 0 }}</span> Suka</span>
      </button>

      <button type="button" onclick="openCommentModal('commentModal-{{ $post->id }}')"
        class="flex items-center gap-1 hover:text-blue-600 transition-colors">
        <i class="fa-regular fa-comment text-base"></i>
        <span id="commentsCount-{{ $post->id }}">{{ $post->comments_count ?? 0 }}</span>
        <span>Komentar</span>
      </button>
    </div>
  </div>
</div>

<div id="commentModal-{{ $post->id }}"
  class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 lg:p-10"
  onclick="closeCommentModal('commentModal-{{ $post->id }}')">

  <div
    class="relative bg-white text-ink rounded-xl overflow-hidden w-full max-w-5xl h-[85vh] flex flex-col md:flex-row shadow-2xl"
    onclick="event.stopPropagation()">

    <button onclick="closeCommentModal('commentModal-{{ $post->id }}')"
      class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-600 hover:text-red-500 text-xl font-bold z-20 cursor-pointer">&times;</button>

    <div
      class="w-full md:w-1/2 bg-black flex items-center justify-center relative overflow-hidden h-64 md:h-full border-b md:border-b-0 md:border-r border-neutral-200">
      @if ($mediaList->isNotEmpty())
        <div class="relative group w-full h-full flex items-center justify-center bg-black">
          <div class="flex w-full h-full transition-transform duration-300 ease-in-out"
            id="modal-slides-{{ $post->id }}">
            @foreach ($mediaList as $item)
              @php
                $isImg = in_array($item['ext'], ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                $isVid = in_array($item['ext'], ['mp4', 'mov', 'webm']);
              @endphp
              <div class="w-full h-full shrink-0 flex items-center justify-center">
                @if ($isImg)
                  <img src="{{ $item['url'] }}" class="w-full h-full object-contain" alt="Post Media">
                @elseif($isVid)
                  <video controls class="w-full h-full object-contain">
                    <source src="{{ $item['url'] }}"
                      type="video/{{ $item['ext'] === 'mov' ? 'quicktime' : $item['ext'] }}">
                  </video>
                @else
                  <div class="p-6 text-center text-white">
                    <i class="fa-solid fa-file-lines text-5xl mb-3 text-neutral-400"></i>
                    <p class="text-small font-semibold truncate max-w-small">{{ $item['filename'] }}</p>
                  </div>
                @endif
              </div>
            @endforeach
          </div>

          @if ($mediaList->count() > 1)
            <button type="button" onclick="moveModalSlide('{{ $post->id }}', -1)"
              class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/60 text-white rounded-full flex items-center justify-center text-sm z-10 cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-chevron-left preview-icon">
                <path d="m15 18-6-6 6-6" />
              </svg>
            </button>
            <button type="button" onclick="moveModalSlide('{{ $post->id }}', 1)"
              class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/60 text-white rounded-full flex items-center justify-center text-sm z-10 cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-chevron-right preview-icon">
                <path d="m9 18 6-6-6-6" />
              </svg>
            </button>
          @endif
        </div>
      @else
        <div class="max-w-md text-center p-6 text-white">
          @if ($post->title)
            <h3 class="text-body-mid font-semibold">{{ $post->title }}</h3>
          @endif
          <p class="text-caption leading-relaxed italic">"{{ $post->content }}"</p>
        </div>
      @endif
    </div>

    <div class="w-full md:w-1/2 flex flex-col h-full bg-white min-w-0">

      <div class="px-4 py-3 border-b border-neutral-100 shrink-0 flex gap-3">
        @if ($postAuthor->avatar_full_url)
          <img src="{{ $postAuthor->avatar_full_url }}" alt="{{ $postAuthor->name }}"
            class="rounded-full size-10 object-cover"
            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
          <div
            class="hidden size-10 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
            {{ Str::upper(Str::substr($postAuthor->name, 0, 1)) }}
          </div>
        @else
          <div
            class="size-10 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
            {{ Str::upper(Str::substr($postAuthor->name, 0, 1)) }}
          </div>
        @endif

        <div class="flex-1 min-w-0">
          <div class="flex flex-col gap-0.5">
            <span
              class="text-body-mid font-semibold text-neutral-900 block leading-tight">{{ $postAuthor->name ?? 'Anonim' }}</span>
            <span class="text-small font-medium text-ink-muted">{{ $post->club->name ?? 'Umum' }}</span>
          </div>

          @if (!empty($post->title))
            <h4 class="text-body-mid font-bold text-ink mt-2 leading-tight wrap-break-words">
              {{ $post->title }}
            </h4>
          @endif

          <p class="text-caption text-ink-secondary">
            {{ $post->content }}
          </p>

          <span class="text-small text-ink-muted mt-1 block leading-none">
            {{ $post->created_at->diffForHumans() }}
          </span>
        </div>
      </div>

      <div class="flex-1 p-4 overflow-y-auto space-y-4">
        <div class="space-y-4">
          @forelse ($post->comments->whereNull('parent_id') as $comment)
            <div class="space-y-2" data-comment-id="{{ $comment->id }}">
              <div class="flex gap-3">
                @if ($postAuthor->avatar_full_url)
                  <img src="{{ $postAuthor->avatar_full_url }}" alt="{{ $postAuthor->name }}"
                    class="rounded-full size-8 object-cover"
                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                  <div
                    class="hidden size-8 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
                    {{ Str::upper(Str::substr($postAuthor->name, 0, 1)) }}
                  </div>
                @else
                  <div
                    class="size-8 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
                    {{ Str::upper(Str::substr($postAuthor->name, 0, 1)) }}
                  </div>
                @endif
                <div class="flex-1 min-w-0">
                  <p class="text-caption text-neutral-800 leading-snug wrap-break-words">
                    <span class="font-semibold text-neutral-900 mr-1.5">{{ $comment->user->name ?? 'User' }}</span>
                    <span>{{ $comment->content }}</span>
                  </p>
                  <div class="flex items-center gap-3 mt-1 text-small text-ink-muted font-medium">
                    <span>{{ $comment->created_at->diffForHumans() }}</span>
                    <button type="button"
                      onclick="replyComment('{{ $post->id }}', '{{ $comment->id }}', '{{ $comment->user->name ?? 'User' }}')"
                      class="hover:text-blue-600 cursor-pointer">Balas</button>
                    @if ($comment->user_id === auth()->id())
                      <button type="button" onclick="deleteComment('{{ $comment->id }}', '{{ $post->id }}')"
                        class="hover:text-red-500 cursor-pointer">Hapus</button>
                    @endif
                    @if (Auth::id() !== $comment->user->id)
                      <button type="button"
                        @click="
                          openReport(
                            'comment',
                            '{{ $comment->id }}',
                            '{{ $comment->user->id }}',
                            '{{ route('reports.store') }}',
                            @js([
    'id' => $comment->id,
    'author_id' => $comment->user->id,
])
                          )"
                        class="hover:text-red-500 cursor-pointer">

                        Laporkan
                      </button>
                    @endif
                  </div>
                </div>
              </div>

              @php
                $getReplies = function ($parentId) use (&$getReplies, $post) {
                    $directReplies = $post->comments->where('parent_id', $parentId);
                    $children = collect();
                    foreach ($directReplies as $reply) {
                        $children->push($reply);
                        $children = $children->merge($getReplies($reply->id));
                    }
                    return $children;
                };

                $allReplies = $getReplies($comment->id);
              @endphp

              @if ($allReplies->isNotEmpty())
                <div class="ml-9 space-y-2.5 border-l-2 border-neutral-100 pl-3 replies-container">
                  @foreach ($allReplies as $reply)
                    <div class="flex gap-2.5" data-comment-id="{{ $reply->id }}">
                      @if ($reply->user->avatar_full_url)
                        <img src="{{ $reply->user->avatar_full_url }}" alt="{{ $reply->user->name }}"
                          class="size-8 rounded-full object-cover"
                          onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                        <div
                          class="hidden size-8 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
                          {{ Str::upper(Str::substr($reply->user->name, 0, 1)) }}
                        </div>
                      @else
                        <div
                          class="size-8 items-center justify-center rounded-full bg-primary/10 text-title font-semibold text-primary flex">
                          {{ Str::upper(Str::substr($reply->user->name, 0, 1)) }}
                        </div>
                      @endif
                      <div class="flex-1 min-w-0">
                        <p class="text-caption text-neutral-800 leading-snug wrap-break-words">
                          <span
                            class="font-semibold text-neutral-900 mr-1.5">{{ $reply->user->name ?? 'User' }}</span>
                          <span>{{ $reply->content }}</span>
                        </p>
                        <div class="flex items-center gap-3 mt-1 text-small text-ink-muted font-normal">
                          <span>{{ $reply->created_at->diffForHumans() }}</span>
                          <button type="button"
                            onclick="replyComment('{{ $post->id }}', '{{ $reply->id }}', '{{ $reply->user->name ?? 'User' }}')"
                            class="hover:text-blue-600 cursor-pointer">Balas</button>
                          @if ($reply->user_id === auth()->id())
                            <button type="button"
                              onclick="deleteComment('{{ $reply->id }}', '{{ $post->id }}')"
                              class="hover:text-red-500 cursor-pointer">Hapus</button>
                          @endif
                          @if (Auth::id() !== $reply->user->id)
                            <button type="button"
                              @click="
                                openReport(
                                  'comment',
                                  '{{ $reply->id }}',
                                  '{{ $reply->user->id }}',
                                  '{{ route('reports.store') }}',
                                  @js([
    'id' => $reply->id,
    'author_id' => $reply->user->id,
])
                                )"
                              class="hover:text-red-500 cursor-pointer">

                              Laporkan
                            </button>
                          @endif
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          @empty
            <p class="text-caption text-ink-muted text-center py-8">Belum ada komentar.</p>
          @endforelse
        </div>
      </div>

      <div class="p-4 border-t border-neutral-100 bg-white shrink-0">
        <div class="flex items-center gap-4 text-neutral-700 mb-2">
          <button type="button" id="likeBtnModal-{{ $post->id }}" onclick="toggleLike('{{ $post->id }}')"
            class="hover:text-red-500 transition-colors">
            <i id="likeIconModal-{{ $post->id }}"
              class="{{ $isLiked ? 'fa-solid text-red-500' : 'fa-regular' }} fa-heart text-xl"></i>
          </button>
          <i class="fa-regular fa-comment text-xl text-neutral-700"></i>
        </div>
        <p class="text-small font-bold text-neutral-900 mb-1">
          <span id="likeCountModal-{{ $post->id }}">{{ $post->likes_count ?? 0 }}</span> Suka
        </p>
        <span
          class="text-small text-neutral-400 uppercase block mb-3 font-semibold">{{ $post->created_at->format('M d, Y') }}</span>

        <form id="commentForm-{{ $post->id }}" onsubmit="submitComment(event, '{{ $post->id }}')"
          class="flex flex-col border-t border-neutral-100 pt-2">
          @csrf
          <input type="hidden" name="parent_id" id="parentId-{{ $post->id }}" value="">

          <div id="replyIndicator-{{ $post->id }}"
            class="hidden flex items-center justify-between text-caption text-neutral-500 bg-neutral-100 px-2 py-1 rounded mb-2">
            <span>Membalas <span id="replyTarget-{{ $post->id }}"
                class="text-ink-secondary font-semibold"></span></span>
            <button type="button" onclick="cancelReply('{{ $post->id }}')"
              class="text-ink-muted hover:text-red-500 font-bold">&times;</button>
          </div>

          <div class="flex items-center gap-2">
            <input type="text" name="content" id="inputContent-{{ $post->id }}"
              placeholder="Tambahkan komentar..." required
              class="flex-1 rounded-md bg-transparent text-caption text-neutral-800 placeholder-ink-muted focus:outline-none">
            <button type="submit"
              class="text-primary hover:text-primary-active text-caption font-semibold cursor-pointer">
              Kirim
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
