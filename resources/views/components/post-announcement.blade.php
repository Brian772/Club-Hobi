<div class="flex flex-row mb-4 pb-2 border border-hairline rounded-md p-4 bg-canvas-soft">
  <div class="min-w-10">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
      <path d="M0 0h24v24H0z" fill="none" />
      <path fill="#000"
        d="M22 11.25a4.25 4.25 0 0 0-3-4.06V3.705a1.247 1.247 0 0 0-1.96-1.025l-5.525 3.825H5.75A2.755 2.755 0 0 0 3 9.255v3.5a2.755 2.755 0 0 0 2.75 2.75H6v4.75c0 .69.56 1.25 1.25 1.25h2.5c.69 0 1.25-.56 1.25-1.25v-4.75h.515l5.525 3.825A1.248 1.248 0 0 0 19 18.305V15.32a4.26 4.26 0 0 0 3-4.06zm-4.5 6.57L11.985 14H9.5v6h-2v-6H5.75c-.69 0-1.25-.56-1.25-1.25v-3.5C4.5 8.56 5.06 8 5.75 8h6.235L17.5 4.18zm1.5-4.125V8.8c.89.455 1.5 1.38 1.5 2.445s-.61 1.99-1.5 2.445z" />
    </svg>
  </div>
  <div class="w-full ml-2">
    <div class="flex flex-row w-full items-center justify-between mb-2">
      <div class="flex flex-row gap-2 justify-center items-center">
      <span class="font-semibold">Pengumuman</span>
      <span class="border border-primary rounded-full px-2 py-1 text-overline text-primary">ANNOUNCEMENT</span>
      </div>
      <span class="text-caption text-ink-muted">{{ $post->created_at->diffForHumans() }}</span>
    </div>
    <div>
      <p class="text-ink text-body-sm md:text-body-md mb-2">{{ $post->content }}</p>
      @if ($post->media_url)
        <img src="{{ $post->media_url }}" class="w-full rounded-lg max-w-100" alt="">
      @endif
      <div class="flex flex-row gap-4 mt-2">
        <div class="flex flex-row gap-2 items-center">
          <button class="fas fa-heart text-ink-muted text-caption cursor-pointer" aria-label="like"></button>
          <span>12</span>
        </div>
        <div class="flex flex-row gap-2 items-center">
          <button class="fas fa-comment text-ink-muted text-caption cursor-pointer" aria-label="comment"></button>
          <span>8</span>
        </div>
      </div>
    </div>
  </div>
</div>
