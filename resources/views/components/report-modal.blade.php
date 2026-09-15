{{-- Report Modal --}}
<div x-show="openReportModal" x-cloak @keydown.escape.window="openReportModal = false"
  class="fixed flex items-center justify-center inset-0 z-50">
  <div @click="openReportModal = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
  </div>
  <div class="fixed p-6 h-max rounded-lg z-50 bg-canvas border border-hairline overflow-hidden w-sm lg:w-2xl">
    <div class="flex flex-col gap-2 mb-4">
      <div class="flex flex-row mb-4 justify-between items-start">
        <div>
          <h3 class="text-title text-ink">
            Laporkan <span x-text="contentType"></span>
          </h3>
          <p x-show="contentType === 'user'" class="text-ink-muted text-caption">Laporkan pengguna ini jika melanggar
            aturan dan ketentuan yang berlaku di orbii.</p>
          <p x-show="contentType === 'postingan'" class="text-ink-muted text-caption">Laporkan postingan ini jika
            melanggar aturan dan ketentuan yang berlaku di orbii.</p>
          <p x-show="contentType === 'comment'" class="text-ink-muted text-caption">Laporkan komentar ini jika melanggar
            aturan dan ketentuan yang berlaku di orbii.</p>
        </div>
        <span class="text-ink-muted text-body-mid hover:text-primary cursor-pointer hover:bg-primary/10 rounded-md p-2"
          @click="closeReport()">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-x">
            <path d="M18 6 6 18" />
            <path d="m6 6 12 12" />
          </svg>
        </span>
      </div>
      <div x-show="contentType === 'user'"
        class="flex flex-row items-center justify-between gap-2 p-2 border border-hairline rounded-lg">
        <div class="flex flex-row items-center gap-2 p-2">
          <img :src="reportTarget.avatar" class="w-10 h-10 border border-hairline rounded-full object-cover"
            alt="">
          <p class="font-semibold" x-text="reportTarget.name"></p>
        </div>
        <div class="p-2">
          <span class="text-ink-muted text-caption">Bergabung</span>
          <p class="text-ink text-caption font-semibold" x-text="reportTarget.joined"></p>
        </div>
      </div>
      <form :action="reportUrl" method="POST">
        @csrf
        <input type="hidden" name="contentType" :value="contentType">
        <input type="hidden" name="contentId" :value="contentId">
        <input type="hidden" name="reportedUserId" :value="reportedUserId">
        <div class="flex flex-col gap-1 mt-4">
          <label for="reason" class="text-body-mid text-ink font-semibold">Deskripsikan alasan:</label>
          <textarea name="reason" id="reason" rows="4"
            class="w-full border border-hairline rounded-lg p-2 text-caption text-ink focus:outline-none focus:ring-1 focus:ring-primary"
            placeholder="Jelaskan secara detail apa yang terjadi." required></textarea>
        </div>
    </div>
    <div class="flex flex-row justify-end gap-2 mt-4">
      <button type="submit"
        class="bg-primary/10 text-primary rounded px-4 py-2 hover:bg-primary cursor-pointer hover:text-white">Kirim Laporan</button>
      <button type="button"
        class="bg-gray-200 border border-hairline rounded text-caption px-4 py-2 text-ink cursor-pointer hover:bg-gray-300"
        @click="closeReport()">Cancel</button>
    </div>
    </form>
  </div>
</div>
