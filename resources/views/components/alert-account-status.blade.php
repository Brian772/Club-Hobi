@if (auth()->user()->status === 'suspended')
  <div
    class="flex flex-row gap-2 items-center justify-center w-full lg:w-max border border-yellow-500 bg-yellow-100 text-yellow-800 px-4 py-2 rounded-md relative"
    role="alert">
    <div>
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fc0"
        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        class="lucide lucide-triangle-alert-icon lucide-triangle-alert">
        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" />
        <path d="M12 9v4" />
        <path d="M12 17h.01" />
      </svg>
    </div>
    <div>
      Akun Anda sedang suspend hingga {{ auth()->user()->suspended_until->format('d M Y') }}.
      Anda hanya bisa melihat konten. <a href="{{ route('appeal.create') }}" class="text-primary underline">Ajukan
        banding</a>.
    </div>
  </div>
@elseif(auth()->user()->status === 'banned')
  <div
    class="flex flex-row gap-2 items-center justify-center w-full lg:w-max mb-4 border border-red-500 bg-red-100 text-red-800 px-4 py-2 rounded-md relative"
    role="alert">
    <div>
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
        stroke="#ff383c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        class="lucide lucide-ban-icon lucide-ban">
        <circle cx="12" cy="12" r="10" />
        <path d="M4.929 4.929 19.07 19.071" />
      </svg>
    </div>
    <div>
      Akun anda telah diblokir. <a href="{{ route('appeal.create') }}" class="text-primary underline">Ajukan
        banding</a>.
    </div>
  </div>
@endif
