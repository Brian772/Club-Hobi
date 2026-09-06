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

  <main class="flex flex-col mt-4 gap-4">
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
          <div class="border mt-2 border-hairline p-2 rounded-lg overflow-x-auto">
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
                      @if ($member->role === 'member')
                        <form action="{{ route('clubs.kick', [$club->id, $member->user->id]) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="submit"
                            class="px-2 py-1 text-accent-red cursor-pointer hover:underline hover:underline-offset-2">
                            Kick
                          </button>
                        </form>
                      @endif
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
          <div class="border mt-2 border-hairline p-2 rounded-lg overflow-x-auto">
            <table class="min-w-full table-auto">
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
                      @if ($member->role === 'moderator')
                        <form action="{{ route('clubs.kick', [$club->id, $member->user->id]) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="submit"
                            class="px-2 py-1 text-accent-red cursor-pointer hover:underline hover:underline-offset-2">
                            Kick
                          </button>
                        </form>
                      @endif
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
                      <form action="{{ route('clubs.join.request.accept', [$club->id, $request->id]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                          class="px-2 py-1 text-primary cursor-pointer hover:underline hover:underline-offset-2">
                          Accept
                        </button>
                      </form>
                      <form action="{{ route('clubs.join.request.reject', [$club->id, $request->id]) }}" method="POST">
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
