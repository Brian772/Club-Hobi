<a href="{{ route('profile.show', ['user' => $member->user->id]) }}" class="w-full flex flex-row justify-between items-center p-4">
  <div class="flex flex-row gap-3 justify-center items-center w-max">
    <div class="min-w-10">
      <img src="{{ $member->user->avatar_full_url }}" alt="{{ $member->user->name }}"
        class="rounded-full w-10 h-10 object-cover">
    </div>
    <div class="flex flex-col gap-2 w-max">
      <h3 class="text-body-mid font-semibold text-ink">{{ $member->user->name }}
        @if ($member->user->role_global === 'admin')
          <span
            class="text-overline text-primary bg-primary/10 rounded-full px-2 py-1 border border-primary font-semibold">Admin</span>
        @endif
      </h3>
      <p class="text-caption text-ink-muted"><span class="text-ink">Hobi:
        </span>{{ implode(', ', $member->user->interest_array ?? []) }}</p>
    </div>
  </div>

  @if ($member->user->role_global === 'admin')
  @else
    @can('admin')
      <form action="{{ route('admin.clubs.kick', [$member->club_id, $member->user_id]) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit"
          class="text-caption text-red-500 flex flex-row gap-2 justify-center items-center border border-red-500 bg-red-50 rounded-md px-4 py-2 hover:bg-red-500 hover:text-white">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-user-round-x-icon lucide-user-round-x">
            <path d="m16.5 16.5 5 5" />
            <path d="M2 21a8 8 0 0 1 11.531-7.18" />
            <path d="m21.5 16.5-5 5" />
            <circle cx="10" cy="8" r="5" />
          </svg>
          Kick Member
        </button>
      </form>
    @endcan
  @endif
</a>
