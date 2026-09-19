@if ($paginator->hasPages())
  <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
    <div class="flex justify-between flex-1 sm:hidden">
      @if ($paginator->onFirstPage())
        <span class="px-4 py-2 text-sm text-ink-muted bg-canvas border border-hairline rounded-md cursor-not-allowed">
          {!! __('pagination.previous') !!}
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}"
          class="px-4 py-2 text-sm text-ink bg-canvas border border-hairline rounded-md hover:bg-hairline">
          {!! __('pagination.previous') !!}
        </a>
      @endif

      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
          class="px-4 py-2 text-sm text-ink bg-canvas border border-hairline rounded-md hover:bg-hairline">
          {!! __('pagination.next') !!}
        </a>
      @else
        <span class="px-4 py-2 text-sm text-ink-muted bg-canvas border border-hairline rounded-md cursor-not-allowed">
          {!! __('pagination.next') !!}
        </span>
      @endif
    </div>

    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-caption text-ink-muted">
          {!! __('Menampilkan') !!}
          <span class="font-medium">{{ $paginator->firstItem() }}</span>
          {!! __('sampai') !!}
          <span class="font-medium">{{ $paginator->lastItem() }}</span>
          {!! __('dari') !!}
          <span class="font-medium">{{ $paginator->total() }}</span>
          {!! __('hasil') !!}
        </p>
      </div>

      <div>
        <span class="relative z-0 inline-flex gap-1">
          {{-- Previous --}}
          @if ($paginator->onFirstPage())
            <span
              class="px-4 py-2 flex flex-row gap-2 items-center justify-center text-sm text-ink-muted bg-canvas border border-hairline rounded-md cursor-not-allowed">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-chevron-left preview-icon">
                <path d="m15 18-6-6 6-6" />
              </svg>
              Previous
            </span>
          @else
            <a href="{{ $paginator->previousPageUrl() }}"
              class="px-4 py-2 flex flex-row gap-2 items-center justify-center text-sm text-ink bg-canvas border border-hairline rounded-md hover:bg-hairline">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-chevron-left preview-icon">
                <path d="m15 18-6-6 6-6" />
              </svg>
              Previous
            </a>
          @endif

          {{-- Nomor halaman --}}
          @foreach ($elements as $element)
            @if (is_string($element))
              <span class="px-4 py-2 text-sm text-ink-muted bg-canvas border border-hairline rounded-md">
                {{ $element }}
              </span>
            @endif

            @if (is_array($element))
              @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                  <span class="px-4 py-2 text-sm font-semibold text-white bg-primary border border-primary rounded-md">
                    {{ $page }}
                  </span>
                @else
                  <a href="{{ $url }}"
                    class="px-4 py-2 flex flex-row gap-2 items-center justify-center text-sm text-ink bg-canvas border border-hairline rounded-md hover:bg-hairline">
                    {{ $page }}
                  </a>
                @endif
              @endforeach
            @endif
          @endforeach

          {{-- Next --}}
          @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
              class="px-4 py-2 flex flex-row gap-2 items-center justify-center text-sm text-ink bg-canvas border border-hairline rounded-md hover:bg-hairline">
              Next
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-chevron-right preview-icon">
                <path d="m9 18 6-6-6-6" />
              </svg>
            </a>
          @else
            <span
              class="px-4 py-2 flex flex-row gap-2 items-center justify-center text-sm text-ink bg-canvas border border-hairline rounded-md hover:bg-hairline cursor-not-allowed">
              Next
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-chevron-right preview-icon">
                <path d="m9 18 6-6-6-6" />
              </svg>
            </span>
          @endif
        </span>
      </div>
    </div>
  </nav>
@endif
