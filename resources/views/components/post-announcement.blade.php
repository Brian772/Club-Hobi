<div class="flex flex-row mb-4 pb-2 border border-hairline rounded-md p-4 bg-canvas-soft">
  <div class="min-w-10">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
      class="lucide lucide-megaphone-icon lucide-megaphone">
      <path
        d="M11 6a13 13 0 0 0 8.4-2.8A1 1 0 0 1 21 4v12a1 1 0 0 1-1.6.8A13 13 0 0 0 11 14H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z" />
      <path d="M6 14a12 12 0 0 0 2.4 7.2 2 2 0 0 0 3.2-2.4A8 8 0 0 1 10 14" />
      <path d="M8 6v8" />
    </svg>
  </div>
  <div class="w-full ml-2">
    <div class="flex flex-col gap-1 w-full items-start mb-4">
      <div class="flex flex-row gap-2 justify-center items-center">
        <span class="font-semibold">Pengumuman</span>
        <span class="bg-primary/10 rounded-full px-2 py-1 text-overline text-primary">ANNOUNCEMENT</span>
      </div>
      <span class="text-caption text-ink-muted">{{ $post->created_at->diffForHumans() }}</span>
    </div>
    <div>
      <p class="text-ink text-body-sm md:text-body-md mb-4">{{ $post->content }}</p>
      @if ($post->media_url)
        <img src="{{ $post->media_url }}" class="w-full rounded-lg max-w-100 overflow-hidden" alt="">
      @endif
      <div class="flex flex-row gap-4 mt-4">
        <div class="flex flex-row gap-2 items-center">
          <button class="cursor-pointer" aria-label="like">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-heart-icon lucide-heart">
              <path
                d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5" />
            </svg>
          </button>
          <span>12</span>
        </div>
        <div class="flex flex-row gap-2 items-center">
          <button class="cursor-pointer" aria-label="comment">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-message-circle-icon lucide-message-circle">
              <path
                d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719" />
            </svg>
          </button>
          <span>8</span>
        </div>
      </div>
    </div>
  </div>
</div>
