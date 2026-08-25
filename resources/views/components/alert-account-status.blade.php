@if (auth()->user()->status === 'suspended')
  <div
    class="flex flex-row gap-2 items-center justify-center w-full mb-4 border border-yellow-500 bg-yellow-100 text-yellow-800 px-4 py-3 rounded-md relative"
    role="alert">
    <div>
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
        <path d="M0 0h24v24H0z" fill="none" />
        <path fill="#fc0"
          d="M2.725 21q-.275 0-.5-.137t-.35-.363t-.137-.488t.137-.512l9.25-16q.15-.25.388-.375T12 3t.488.125t.387.375l9.25 16q.15.25.138.513t-.138.487t-.35.363t-.5.137zm1.725-2h15.1L12 6zm8.263-1.287Q13 17.425 13 17t-.288-.712T12 16t-.712.288T11 17t.288.713T12 18t.713-.288m0-3Q13 14.425 13 14v-3q0-.425-.288-.712T12 10t-.712.288T11 11v3q0 .425.288.713T12 15t.713-.288M12 12.5" />
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
    class="flex flex-row gap-2 items-center justify-center w-full mb-4 border border-red-500 bg-red-100 text-red-800 px-4 py-3 rounded-md relative"
    role="alert">
    <div>
      <svg xmlns="http://www.w3.org/2000/svg" width="1024" height="1024" viewBox="0 0 1024 1024">
        <path d="M0 0h1024v1024H0z" fill="none" />
        <path fill="#ff383c"
          d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448s448-200.6 448-448S759.4 64 512 64m0 820c-205.4 0-372-166.6-372-372c0-89 31.3-170.8 83.5-234.8l523.3 523.3C682.8 852.7 601 884 512 884m288.5-137.2L277.2 223.5C341.2 171.3 423 140 512 140c205.4 0 372 166.6 372 372c0 89-31.3 170.8-83.5 234.8" />
      </svg>
    </div>
    <div>
      Akun anda telah diblokir. <a href="{{ route('appeal.create') }}" class="text-primary underline">Ajukan
        banding</a>.
    </div>
  </div>
@endif
