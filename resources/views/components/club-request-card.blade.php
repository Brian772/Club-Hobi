<a href="{{ route('admin.clubs.request.show', $request->id) }}"
  class="flex flex-row gap-2 lg:gap-4 items-center justify-start mb-4 rounded-lg border border-hairline bg-canvas-soft p-4 hover:shadow-md transition-shadow duration-300">
  <div class="flex flex-col lg:flex-row gap-2 w-full h-full items-start justify-start">
    <div class="w-full lg:w-64">
      <img src="{{ Storage::url($request->cover_url) }}" alt="{{ $request->name }} Cover"
        class="w-full lg:w-64 h-40 rounded-lg object-cover">
    </div>
    <div class="w-full flex flex-col gap-2">
      <h3 class="text-heading-3 flex flex-row items-center gap-2 justify-start text-ink">{{ $request->name }}
        @if ($request->status === 'pending')
          <span
            class="rounded-full bg-accent-yellow/10 text-accent-yellow text-overline px-2 py-1">{{ Str::upper($request->status) }}</span>
        @elseif ($request->status === 'approved')
          <span
            class="rounded-full bg-accent-green/10 text-accent-green text-overline px-2 py-1">{{ Str::upper($request->status) }}</span>
        @else
          <span
            class="rounded-full bg-accent-red/10 text-accent-red text-overline px-2 py-1">{{ Str::upper($request->status) }}</span>
        @endif
      </h3>
      <p class="text-body-mid text-ink-muted">{{ $request->description }}</p>
      <p class="text-caption text-ink font-bold">Requested by: <span
          class="text-ink-muted font-normal">{{ $request->requester->name }}</span></p>
    </div>
  </div>
  <div class="hidden lg:block hover:transform hover:translate-x-1.5 transition-transform duration-300">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
      class="lucide lucide-arrow-right-icon lucide-arrow-right">
      <path d="m12 5 7 7-7 7" />
    </svg>
  </div>
</a>
