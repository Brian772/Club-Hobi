@extends('layouts.app')

@section('content')
  <header class="flex flex-row gap-2 lg:gap-4 items-center justify-start mb-3">
    <a href="{{ route('clubs.show', $club->id) }}" class="text-ink-muted">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
        <path d="M0 0h16v9H0z" fill="none" />
        <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
        <path fill="currentColor"
          d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
      </svg>
    </a>
    <h2 class="text-title lg:text-heading-2 flex flex-row items-center gap-2 justify-center text-ink">Settings -
      {{ $club->name }}
    </h2>
  </header>

  <main x-data="{ openPromote: false, openKick: false, openDemote: false, openDelete: false }" class="flex flex-col mt-4 gap-4">
    {{-- Edit Club --}}
    @can('update', $club)
      <section class="border border-hairline rounded-lg p-4">
        <header class="w-full flex flex-col mb-4">
          <h3 class="text-title lg:text-heading-3 text-ink">Edit Club</h3>
          <p class="text-body text-ink-muted">Update your club information below.</p>
        </header>
        <div class="max-w-4xl">
          <form action="{{ route('clubs.update', $club->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="flex flex-col lg:flex-row gap-2 lg:gap-4 w-full">
              <div class="w-full lg:w-1/2 flex flex-col gap-2">
                <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="Club Cover" id="coverPreview"
                  class="w-full h-64 rounded-lg object-cover border border-hairline">
              </div>
              <div class="flex flex-col items-start">
                <div class="w-full lg:w-1/2 flex flex-col gap-1 h-full">
                  <input type="file" id="cover" name="cover" accept="image/jpeg, image/png" class="hidden">
                  <button type="button" id="editCover" onclick="document.getElementById('cover').click()"
                    class="rounded-lg bg-canvas-soft border border-hairline text-body-mid mt-4 lg:mt-0 text-ink w-max py-2 px-4 flex flex-row gap-2 items-center justify-center cursor-pointer hover:bg-primary hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-pencil-icon lucide-pencil">
                      <path
                        d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                      <path d="m15 5 4 4" />
                    </svg>
                    Edit Cover
                  </button>
                  <span class="text-caption text-ink-muted w-max">JPEG/PNG, Max 2MB</span>
                  <p id="coverError" class="text-caption text-accent-red"></p>
                </div>
                <div class="w-full lg:w-1/2">
                  <p class="text-body-mid w-max text-ink font-semibold">Category : <span
                      class="text-ink-muted">{{ $club->hobby->name }}</span></p>
                </div>
              </div>
            </div>
            <div class="w-full h-max flex flex-col gap-3 mt-4">
              <div class="w-full flex flex-col gap-2">
                <x-input-label for="name" :value="__('Club Name')" />
                <input type="text" name="name"
                  class="w-full rounded-md border border-hairline px-4 py-2 focus:ring-primary focus:border-primary"
                  id="name" value="{{ $club->name }}" required>
              </div>
              <div class="w-full flex flex-col gap-2">
                <x-input-label for="description" :value="__('Club Description')" />
                <textarea name="description"
                  class="w-full rounded-md border border-hairline px-4 py-2 focus:ring-primary focus:border-primary" id="description"
                  rows="4">{{ $club->description }}</textarea>
              </div>

              <button type="submit"
                class="w-max rounded-lg bg-primary text-white py-2 px-4 flex flex-row justify-center items-center gap-2 hover:bg-primary-active cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-download-icon lucide-download">
                  <path d="M12 15V3" />
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                  <path d="m7 10 5 5 5-5" />
                </svg>
                Update Club
              </button>
            </div>
          </form>
        </div>
      </section>
    @endcan

    {{-- Manage Member --}}
    <section x-data="{ tab: 'member' }" class="border border-hairline rounded-lg p-4">
      <header class="w-full flex flex-col mb-4">
        <h3 class="text-title lg:text-heading-3 text-ink">Manage Member</h3>
        <p class="text-body text-ink-muted">Manage your club members and moderators.</p>
      </header>
      <div class="w-full h-max flex flex-row gap-4 border-b border-hairline overflow-x-auto">
        <button @click="tab = 'member'"
          :class="tab === 'member' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted'"
          class="px-4 py-2 text-body font-semibold focus:outline-none">
          Member
        </button>
        <button @click="tab = 'moderator'"
          :class="tab === 'moderator' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted'"
          class="px-4 py-2 text-body font-semibold focus:outline-none">
          Moderator
        </button>
        <button @click="tab = 'request'"
          :class="tab === 'request' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted'"
          class="px-4 py-2 text-body font-semibold focus:outline-none">
          Request
          @if ($joinRequests->count() > 0)
            <span
              class="ml-2 inline-flex items-center justify-center py-1 px-2 text-overline font-semiboldleading-none text-white bg-accent-red rounded-full">
              {{ $joinRequests->count() }}
            </span>
          @endif
        </button>
      </div>

      {{-- Member --}}
      <div x-show="tab === 'member'" class="min-w-full">
        @if ($members->isEmpty())
          <p class="text-caption text-ink-muted mt-4">Belum ada member di klub ini.</p>
        @else
          <div class="border mt-2 border-hairline p-2 rounded-lg overflow-x-auto lg:overflow-visible">
            <table class="min-w-full table-auto">
              <thead class="border-b border-hairline">
                <tr>
                  <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                    Name
                  </th>
                  <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                    role
                  </th>
                  <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                    join
                    date</th>
                  <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                    action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($members as $member)
                  <tr class="odd:bg-gray-100 even:bg-canvas">
                    <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $member->user->name }}</td>
                    <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $member->role }}</td>
                    <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $member->joined_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">
                      @if ($member->user->id !== Auth::user()->id)
                        <div x-data="{ MenuOpen: false }" class="relative">
                          <button type="button" x-ref="button" @click="MenuOpen = true"
                            class="text-ink-muted text-body-mid rounded-full p-2 hover:bg-hairline">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round" class="lucide lucide-ellipsis-vertical">
                              <circle cx="12" cy="12" r="1" />
                              <circle cx="12" cy="5" r="1" />
                              <circle cx="12" cy="19" r="1" />
                            </svg>
                          </button>

                          <div x-show="MenuOpen" x-cloak @keydown.escape.window="MenuOpen = false">
                            <div x-anchor.noflip="$refs.button" x-transition:enter="transition ease-out duration-200"
                              x-transition:enter-start="opacity-0 -translate-y-2"
                              x-transition:enter-end="opacity-100 translate-y-0"
                              x-transition:leave="transition ease-in duration-200"
                              x-transition:leave-start="opacity-100 translate-y-0"
                              x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="MenuOpen = false"
                              @click.stop
                              class="z-50 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
                              <a href="{{ route('profile.show', ['user' => $member->user->id]) }}"
                                class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round">
                                  <circle cx="12" cy="8" r="5" />
                                  <path d="M20 21a8 8 0 0 0-16 0" />
                                </svg>
                                Lihat Profil
                              </a>
                              <a href="{{ route('messages.index', ['conversation' => $member->user->id]) }}"
                                class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round"
                                  class="lucide lucide-message-circle-icon lucide-message-circle">
                                  <path
                                    d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719" />
                                </svg>
                                Kirim Pesan
                              </a>
                              @if ($member->role === 'member' || $member->role === 'moderator')
                                @can('isOwner', $club)
                                  @if ($member->role === 'member')
                                    <button type="button" @click="openPromote = true"
                                      class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md w-full text-primary hover:bg-primary/10">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-layer-arrow-up">
                                        <path d="M12 14V4" />
                                        <path
                                          d="M7.674 10.774 2.58 13.09a1 1 0 000 1.822l8.6 3.91a2 2 0 001.65 0l8.58-3.9a1 1 0 00.59-.92 1 1 0 00-.59-.922l-5.078-2.308" />
                                        <path d="m9 7 3-3 3 3" />
                                      </svg>
                                      Promote To Moderator
                                    </button>
                                  @endif
                                  @if ($member->role === 'moderator')
                                    <button type="button" @click="openDemote = true"
                                      class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md w-full text-primary hover:bg-primary/10">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-layer-arrow-down">
                                        <path d="M12 10v10" />
                                        <path d="M22 10a1 1 0 01-.59.92l-5.077 2.308" />
                                        <path
                                          d="M22.017 10.005a1 1 0 00-.597-.916l-8.59-3.91a2 2 0 00-1.66.001L2.6 9.08a1 1 0 00-.02 1.831l5.093 2.316" />
                                        <path d="m9 17 3 3 3-3" />
                                      </svg>
                                      Demote To Moderator
                                    </button>
                                  @endif
                                @endcan
                                <button type="button" @click="openKick = true"
                                  class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md w-full text-accent-red hover:bg-accent-red/10">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-arrow-right-from-line">
                                    <path d="M3 5v14" />
                                    <path d="M21 12H7" />
                                    <path d="m15 18 6-6-6-6" />
                                  </svg>
                                  Kick
                                </button>
                              @endif
                            </div>
                          </div>
                        </div>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          {{ $members->links() }}
        @endif
      </div>

      {{-- Moderator --}}
      <div x-show="tab === 'moderator'" class="min-w-full">
        @if ($moderator->isEmpty())
          <p class="text-caption text-ink-muted mt-4">Tidak ada moderator di klub ini.</p>
        @else
          <div id="container" class="border mt-2 border-hairline p-2 rounded-lg overflow-x-auto lg:overflow-visible">
            <table class="min-w-full h-max table-auto">
              <thead class="border-b border-hairline">
                <tr>
                  <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                    Name</th>
                  <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                    role</th>
                  <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                    join date</th>
                  <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                    action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($moderator as $member)
                  <tr class="odd:bg-gray-100 even:bg-canvas">
                    <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $member->user->name }}</td>
                    <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $member->role }}</td>
                    <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $member->joined_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">
                      @if ($member->user->id !== Auth::user()->id)
                        <div x-data="{ MenuOpen: false }" class="relative">
                          <button type="button" x-ref="button" @click="MenuOpen = true"
                            class="text-ink-muted text-body-mid rounded-full p-2 hover:bg-hairline">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round" class="lucide lucide-ellipsis-vertical">
                              <circle cx="12" cy="12" r="1" />
                              <circle cx="12" cy="5" r="1" />
                              <circle cx="12" cy="19" r="1" />
                            </svg>
                          </button>

                          <div x-show="MenuOpen" x-cloak @keydown.escape.window="MenuOpen = false">
                            <div x-anchor.noflip="$refs.button" x-transition:enter="transition ease-out duration-200"
                              x-transition:enter-start="opacity-0 -translate-y-2"
                              x-transition:enter-end="opacity-100 translate-y-0"
                              x-transition:leave="transition ease-in duration-200"
                              x-transition:leave-start="opacity-100 translate-y-0"
                              x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="MenuOpen = false"
                              class="z-50 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
                              <a href="{{ route('profile.show', ['user' => $member->user->id]) }}"
                                class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round">
                                  <circle cx="12" cy="8" r="5" />
                                  <path d="M20 21a8 8 0 0 0-16 0" />
                                </svg>
                                Lihat Profil
                              </a>
                              <a href="{{ route('messages.index', ['conversation' => $member->user->id]) }}"
                                class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round"
                                  class="lucide lucide-message-circle-icon lucide-message-circle">
                                  <path
                                    d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719" />
                                </svg>
                                Kirim Pesan
                              </a>
                              @if ($member->role === 'member' || $member->role === 'moderator')
                                @can('isOwner', $club)
                                  @if ($member->role === 'member')
                                    <button type="button" @click="openPromote = true"
                                      class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md w-full text-primary hover:bg-primary/10">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-layer-arrow-up">
                                        <path d="M12 14V4" />
                                        <path
                                          d="M7.674 10.774 2.58 13.09a1 1 0 000 1.822l8.6 3.91a2 2 0 001.65 0l8.58-3.9a1 1 0 00.59-.92 1 1 0 00-.59-.922l-5.078-2.308" />
                                        <path d="m9 7 3-3 3 3" />
                                      </svg>
                                      Promote To Moderator
                                    </button>
                                  @endif
                                  @if ($member->role === 'moderator')
                                    <button type="button" @click="openDemote = true"
                                      class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md w-full text-primary hover:bg-primary/10">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-layer-arrow-down">
                                        <path d="M12 10v10" />
                                        <path d="M22 10a1 1 0 01-.59.92l-5.077 2.308" />
                                        <path
                                          d="M22.017 10.005a1 1 0 00-.597-.916l-8.59-3.91a2 2 0 00-1.66.001L2.6 9.08a1 1 0 00-.02 1.831l5.093 2.316" />
                                        <path d="m9 17 3 3 3-3" />
                                      </svg>
                                      Demote To Moderator
                                    </button>
                                  @endif
                                @endcan
                                <button type="button" @click="openKick = true"
                                  class="flex flex-row gap-2 items-center px-4 py-2 text-caption rounded-md w-full text-accent-red hover:bg-accent-red/10">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-arrow-right-from-line">
                                    <path d="M3 5v14" />
                                    <path d="M21 12H7" />
                                    <path d="m15 18 6-6-6-6" />
                                  </svg>
                                  Kick
                                </button>
                              @endif
                            </div>
                          </div>
                        </div>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

      {{-- Join Request --}}
      <div x-show="tab === 'request'" class="min-w-full">
        @if ($joinRequests->isEmpty())
          <p class="text-caption text-ink-muted mt-4">Tidak ada permintaan bergabung.</p>
        @else
          <div class="border mt-2 border-hairline p-2 rounded-lg overflow-x-auto">
            <table class="min-w-full table-auto">
              <thead class="border-b border-hairline">
                <tr>
                  <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                    Name</th>
                  <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                    Request date</th>
                  <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                    action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($joinRequests as $request)
                  <tr class="odd:bg-gray-100 even:bg-canvas">
                    <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $request->user->name }}</td>
                    <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">
                      {{ $request->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 h-16 whitespace-nowrap text-caption flex flex-row gap-2">
                      <form action="{{ route('clubs.join.request.accept', [$club->id, $request->id]) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                          class="px-2 py-1 text-primary cursor-pointer hover:underline hover:underline-offset-2">
                          Accept
                        </button>
                      </form>
                      <form action="{{ route('clubs.join.request.reject', [$club->id, $request->id]) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                          class="px-2 py-1 text-accent-red cursor-pointer hover:underline hover:underline-offset-2">
                          Reject
                        </button>
                      </form>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </section>

    {{-- Delete Club --}}
    @can('isOwner', $club)
      <section class="p-4 mt-12">
        <header class="w-full flex flex-col mb-4">
          <h3 class="text-title lg:text-heading-3 text-ink">Danger Zone</h3>
        </header>

        <main class="flex flex-col gap-2 p-4 border border-accent-red rounded-lg">
          <div class="flex flex-row items-center justify-between ">
            <div class="flex flex-col gap-1">
              <h3 class="text-title text-ink">Delete Club</h3>
              <p class="text-body-mid text-ink-muted">Delete {{ $club->name }}. If you delete this club, you will not be
                able to recover it.</p>
            </div>
            <button type="button" @click="openDelete = true"
              class="border border-accent-red px-4 py-2 rounded-md text-accent-red bg-accent-red/10 hover:text-white hover:bg-accent-red">
              Delete
            </button>
          </div>
        </main>
      </section>
    @endcan

    {{-- Promote Modal --}}
    <div x-show="openPromote" x-cloak @keydown.escape.window="openPromote = false"
      class="fixed flex items-center justify-center inset-0 z-50">
      <div @click="openPromote = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
      </div>
      <div class="fixed p-3 h-max rounded-lg z-50 w-max bg-canvas border border-hairline overflow-hidden">
        <div class="flex flex-col gap-2 mb-4">
          <h3 class="text-title text-ink">
            Apakah anda yakin?
          </h3>
          <p class="text-body-mid text-ink-muted">{{ $member->user_name }} akan menjadi moderator</p>
        </div>
        <div class="flex flex-row gap-2">
          <form action="{{ route('clubs.promote', [$club->id, $member->user->id]) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit"
              class="flex flex-row gap-2 items-center px-4 py-2 text-caption bg-primary/10 rounded-md w-max text-primary hover:text-white hover:bg-primary">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-layer-arrow-up">
                <path d="M12 14V4" />
                <path
                  d="M7.674 10.774 2.58 13.09a1 1 0 000 1.822l8.6 3.91a2 2 0 001.65 0l8.58-3.9a1 1 0 00.59-.92 1 1 0 00-.59-.922l-5.078-2.308" />
                <path d="m9 7 3-3 3 3" />
              </svg>
              Promote To Moderator
            </button>
          </form>
          <button type="button"
            class="bg-gray-200 border border-hairline rounded-md text-caption px-4 py-2 text-ink hover:text-white hover:bg-gray-600"
            @click="openPromote = false">Cancel</button>
        </div>
      </div>
    </div>

    {{-- Demote Modal --}}
    <div x-show="openDemote" x-cloak @keydown.escape.window="openDemote = false"
      class="fixed flex items-center justify-center inset-0 z-50">
      <div @click="openDemote = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
      </div>
      <div class="fixed p-3 h-max rounded-lg z-50 w-max bg-canvas border border-hairline overflow-hidden">
        <div class="flex flex-col gap-2 mb-4">
          <h3 class="text-title text-ink">
            Apakah anda yakin?
          </h3>
          <p class="text-body-mid text-ink-muted">{{ $member->user_name }} akan menjadi member</p>
        </div>
        <div class="flex flex-row gap-2">
          <form action="{{ route('clubs.demote', [$club->id, $member->user->id]) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit"
              class="flex flex-row gap-2 items-center px-4 py-2 text-caption bg-primary/10 rounded-md w-max text-primary hover:text-white hover:bg-primary">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-layer-arrow-down">
                <path d="M12 10v10" />
                <path d="M22 10a1 1 0 01-.59.92l-5.077 2.308" />
                <path
                  d="M22.017 10.005a1 1 0 00-.597-.916l-8.59-3.91a2 2 0 00-1.66.001L2.6 9.08a1 1 0 00-.02 1.831l5.093 2.316" />
                <path d="m9 17 3 3 3-3" />
              </svg>
              Demote To Member
            </button>
          </form>
          <button type="button"
            class="bg-gray-200 border border-hairline rounded-md text-caption px-4 py-2 text-ink hover:text-white hover:bg-gray-600"
            @click="openDemote = false">Cancel</button>
        </div>
      </div>
    </div>

    {{-- Kick Modal --}}
    <div x-show="openKick" x-cloak @keydown.escape.window="openKick = false"
      class="fixed flex items-center justify-center inset-0 z-50">
      <div @click="openKick = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
      </div>
      <div class="fixed p-3 h-max rounded-lg z-50 w-max bg-canvas border border-hairline overflow-hidden">
        <div class="flex flex-col gap-2 mb-4">
          <h3 class="text-title text-ink">
            Apakah anda yakin?
          </h3>
          <p class="text-body-mid text-ink-muted">{{ $member->user_name }} akan dikeluarkan dari klub</p>
        </div>
        <div class="flex flex-row gap-2">
          <form action="{{ route('clubs.kick', [$club->id, $member->user->id]) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
              class="flex flex-row gap-2 items-center px-4 py-2 text-caption bg-primary/10 rounded-md w-max text-accent-red hover:text-white hover:bg-accent-red">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-arrow-right-from-line">
                <path d="M3 5v14" />
                <path d="M21 12H7" />
                <path d="m15 18 6-6-6-6" />
              </svg>
              Kick
            </button>
          </form>
          <button type="button"
            class="bg-gray-200 border border-hairline rounded-md text-caption px-4 py-2 text-ink hover:text-white hover:bg-gray-600"
            @click="openKick = false">Cancel</button>
        </div>
      </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="openDelete" x-cloak @keydown.escape.window="openDelete = false"
      class="fixed flex items-center justify-center inset-0 z-50">
      <div @click="openDelete = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
      </div>
      <div class="fixed p-3 h-max rounded-lg z-50 w-max bg-canvas border border-hairline overflow-hidden">
        <div class="flex flex-col gap-2 mb-4">
          <h3 class="text-title text-ink">
            Apakah anda yakin?
          </h3>
          <p class="text-body-mid text-ink-muted">aksi ini tidak akan bisa di kembalikan</p>
        </div>
        <div class="flex flex-row gap-2">
          <form action="{{ route('clubs.delete', $club->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
              class="flex flex-row gap-2 items-center px-4 py-2 text-caption bg-primary/10 rounded-md w-max text-accent-red hover:text-white hover:bg-accent-red">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-trash">
                <path d="M10 11v6" />
                <path d="M14 11v6" />
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                <path d="M3 6h18" />
                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
              </svg>
              Delete
            </button>
          </form>
          <button type="button"
            class="bg-gray-200 border border-hairline rounded-md text-caption px-4 py-2 text-ink hover:text-white hover:bg-gray-600"
            @click="openDelete = false">Cancel</button>
        </div>
      </div>
    </div>
  </main>

  @push('scripts')
    <script>
      document.addEventListener('turbo:load', function() {
        document.getElementById('cover').addEventListener('change', function(e) {
          const file = e.target.files[0];
          const errorEl = document.getElementById('coverError');
          errorEl.classList.add('hidden');

          if (!file) return;

          if (!['image/jpeg', 'image/png'].includes(file.type)) {
            errorEl.textContent = 'Hanya file JPEG dan PNG yang diperbolehkan.';
            errorEl.classList.remove('hidden');
            e.target.value = '';
            return;
          }

          if (file.size > 2 * 1024 * 1024) {
            errorEl.textContent = 'Ukuran file tidak boleh lebih dari 2MB.';
            errorEl.classList.remove('hidden');
            e.target.value = '';
            return;
          }

          const reader = new FileReader();
          reader.onload = function(e) {
            document.getElementById('coverPreview').src = e.target.result;
          };
          reader.readAsDataURL(file);
        });
      })
    </script>
  @endpush
@endsection
