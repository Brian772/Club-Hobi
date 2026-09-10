<?php

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public string $tab = 'all';
    public $search = '';
    public $reason = '';
    public $suspendDate = null;

    public ?string $selectUserId = null;
    public bool $showSuspendModal = false;
    public bool $showBanModal = false;
    public bool $showUnbanModal = false;
    public bool $showUnsuspendModal = false;

    public function rules(): array
    {
        return [
            'reason' => 'required|string|max:255',
            'suspendDate' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'suspendDate.required' => 'Please provide a suspension date.',
            'suspendDate.date' => 'The suspension date must be a valid date.',
            'reason.required' => 'Please provide a reason.',
            'reason.string' => 'The reason must be a string.',
            'reason.max' => 'The reason may not be greater than 255 characters.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function confirmSuspend(string $userId): void
    {
        $this->reset('reason');
        $this->resetValidation();
        $this->selectUserId = $userId;
        $this->showSuspendModal = true;
    }

    public function confirmBan(string $userId): void
    {
        $this->reset('reason');
        $this->resetValidation();
        $this->selectUserId = $userId;
        $this->showBanModal = true;
    }

    public function confirmUnban(string $userId): void
    {
        $this->selectUserId = $userId;
        $this->showUnbanModal = true;
    }

    public function confirmUnsuspend(string $userId): void
    {
        $this->selectUserId = $userId;
        $this->showUnsuspendModal = true;
    }

    public function suspendUser(): void
    {
        $this->validate();

        if ($this->selectUserId) {
            User::whereKey($this->selectUserId)->update(['status' => 'suspended', 'reason' => $this->reason, 'suspended_until' => $this->suspendDate]);
        }

        $this->reset(['selectUserId', 'showSuspendModal']);
    }

    public function banUser(): void
    {
        $this->validate();

        if ($this->selectUserId) {
            User::whereKey($this->selectUserId)->update(['status' => 'banned', 'reason' => $this->reason]);
        }

        $this->reset(['selectUserId', 'showBanModal']);
    }

    public function unbanUser(): void
    {
        if ($this->selectUserId) {
            User::whereKey($this->selectUserId)->update(['status' => 'active', 'reason' => null]);
        }

        $this->reset(['selectUserId', 'showUnbanModal']);
    }

    public function UnsuspendUser(): void
    {
        if ($this->selectUserId) {
            User::whereKey($this->selectUserId)->update(['status' => 'active', 'suspended_until' => null, 'reason' => null]);
        }

        $this->reset(['selectUserId', 'showUnsuspendModal']);
    }

    public function closeModal(): void
    {
        $this->reset(['selectUserId', 'showSuspendModal', 'showBanModal', 'showUnbanModal', 'showUnsuspendModal', 'reason', 'suspendDate']);
        $this->resetValidation();
    }

    public function with(): array
    {
        $query = User::query()->when($this->search, function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%');
        });

        match ($this->tab) {
            'active' => $query->where('status', 'active'),
            'suspend' => $query->where('status', 'suspended'),
            'banned' => $query->where('status', 'banned'),
            default => null,
        };

        return [
            'users' => $query->paginate(20),
        ];
    }
};
?>

<div>
  <header class="flex flex-col lg:flex-row gap-2 lg:items-center justify-start lg:justify-between mb-4">
    <h1 class="text-2xl font-bold">User Management</h1>
    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search users..."
      class="lg:w-1/3 px-4 py-2 w-full border border-hairline rounded-md focus:outline-none focus:ring focus:border-primary" />
  </header>

  <main class="flex flex-col gap-4 border border-hairrline p-4 rounded-lg">
    <div class="w-full h-max flex flex-row gap-4 border-b border-hairline overflow-x-auto">
      <button wire:click="setTab('all')"
        class="px-4 py-2 text-body font-semibold focus:outline-none {{ $tab === 'all' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted' }}">
        All Users
      </button>
      <button wire:click="setTab('active')"
        class="px-4 py-2 text-body font-semibold focus:outline-none {{ $tab === 'active' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted' }}">
        Active
      </button>
      <button wire:click="setTab('suspend')"
        class="px-4 py-2 text-body font-semibold focus:outline-none {{ $tab === 'suspend' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted' }}">
        Suspended
      </button>
      <button wire:click="setTab('banned')"
        class="px-4 py-2 text-body font-semibold focus:outline-none {{ $tab === 'banned' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted' }}">
        Banned
      </button>
    </div>

    <section class="flex flex-col gap-4" wire:loading.class="opacity-50 pointer-events-none">
      @if ($users->isEmpty())
        <p class="text-gray-600">No users found.</p>
      @else
        <div class="border mt-2 border-hairline p-2 rounded-lg overflow-x-auto lg:overflow-visible">
          <table class="w-full table-auto">
            <thead class="border-b border-hairline">
              <tr>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">Name
                </th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">Role
                </th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  Status
                </th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  Join Date
                </th>
                <th scope="col" class="py-4 px-6 text-start text-body-sm font-semibold text-ink-faint uppercase">
                  Action
                </th>
              </tr>
            </thead>
            <tbody>
              @foreach ($users as $user)
                <tr wire:key="user-{{ $user->id }}" class="even:bg-white odd:bg-gray-100">
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $user->name }}</td>
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $user->role_global }}</td>
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $user->status }}</td>
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">{{ $user->created_at->format('Y-m-d') }}
                  </td>
                  <td class="px-6 py-4 h-16 whitespace-nowrap text-caption">
                    @if ($user->id !== Auth::user()->id)
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
                            class="z-30 mt-2 w-max p-2 bg-canvas border border-hairline rounded-lg shadow-lg overflow-hidden">
                            <a href="{{ route('profile.show', ['user' => $user->id]) }}"
                              class="flex flex-row w-full gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-user-round">
                                <circle cx="12" cy="8" r="5" />
                                <path d="M20 21a8 8 0 0 0-16 0" />
                              </svg>
                              Lihat Profil
                            </a>
                            @if ($user->status === 'active')
                              <a href="{{ route('messages.index', ['conversation' => $user->id]) }}"
                                class="flex flex-row w-full gap-2 items-center px-4 py-2 text-caption rounded-md text-ink hover:bg-hairline">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round"
                                  class="lucide lucide-message-circle-icon lucide-message-circle">
                                  <path
                                    d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719" />
                                </svg>
                                Kirim Pesan
                              </a>
                              <button type="button" wire:click="confirmSuspend('{{ $user->id }}')"
                                class="flex flex-row w-full gap-2 items-center px-4 py-2 text-caption rounded-md text-accent-yellow hover:bg-accent-yellow/10">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert">
                                  <circle cx="12" cy="12" r="10" />
                                  <line x1="12" x2="12" y1="8" y2="12" />
                                  <line x1="12" x2="12.01" y1="16" y2="16" />
                                </svg>
                                Suspend User
                              </button>
                            @endif
                            @if ($user->status === 'suspended')
                              <button type="button" wire:click="confirmUnsuspend('{{ $user->id }}')"
                                class="flex flex-row w-full gap-2 items-center px-4 py-2 text-caption rounded-md text-accent-yellow hover:bg-accent-yellow/10">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert">
                                  <circle cx="12" cy="12" r="10" />
                                  <line x1="12" x2="12" y1="8" y2="12" />
                                  <line x1="12" x2="12.01" y1="16" y2="16" />
                                </svg>
                                Unsuspend User
                              </button>
                            @endif
                            @if ($user->status !== 'banned')
                              <button type="button" wire:click="confirmBan('{{ $user->id }}')"
                                class="flex flex-row w-full gap-2 items-center px-4 py-2 text-caption rounded-md text-accent-red hover:bg-accent-red/10">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ban">
                                  <circle cx="12" cy="12" r="10" />
                                  <path d="M4.929 4.929 19.07 19.071" />
                                </svg>
                                Ban User
                              </button>
                            @endif
                            @if ($user->status === 'banned')
                              <button type="button" wire:click="confirmUnban('{{ $user->id }}')"
                                class="flex flex-row w-full gap-2 items-center px-4 py-2 text-caption rounded-md text-accent-red hover:bg-accent-red/10">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ban">
                                  <circle cx="12" cy="12" r="10" />
                                  <path d="M4.929 4.929 19.07 19.071" />
                                </svg>
                                Unban User
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
        <div>
          {{ $users->links() }}
        </div>
      @endif

      {{-- Suspend Modal --}}
      @if ($showSuspendModal)
        <div x-data @keydown.escape.window="$wire.showSuspendModal = false">
          <div class="fixed inset-0 bg-black/50 z-40"></div>
          <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="bg-canvas border border-hairline rounded-lg shadow-lg p-6 w-full max-w-md">
              <div class="flex flex-col gap-2 mb-4">
                <h2 class="text-lg font-semibold">Suspend User</h2>
                <p>Are you sure you want to suspend this user? This action can be reversed later.</p>
              </div>
              <div class="flex flex-col mb-4">
                <div class="mb-4">
                  <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Suspension</label>
                  <input type="text" wire:model="reason" id="reason"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                  @error('reason')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                  @enderror
                </div>
                <div class="mb-4">
                  <label for="suspendDate" class="block text-sm font-medium text-gray-700">Suspend Until</label>
                  <input type="date" wire:model="suspendDate" id="suspendDate" placeholder="DD-MM-YYYY"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                  @error('suspendDate')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="flex justify-end gap-2">
                <button type="button" wire:click="suspendUser"
                  class="flex flex-row gap-2 items-center px-4 py-2 bg-accent-yellow/10 text-accent-yellow hover:bg-accent-yellow hover:text-white rounded">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-circle-alert">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" x2="12" y1="8" y2="12" />
                    <line x1="12" x2="12.01" y1="16" y2="16" />
                  </svg>
                  Suspend User</button>
                <button wire:click="closeModal"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      @endif

      {{-- Ban Modal --}}
      @if ($showBanModal)
        <div x-data x-cloak @keydown.escape.window="$wire.showBanModal = false">
          <div class="fixed inset-0 bg-black/50 z-40"></div>
          <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="bg-canvas border border-hairline rounded-lg shadow-lg p-6 w-full max-w-md">
              <div class="flex flex-col gap-2 mb-4">
                <h2 class="text-lg font-semibold">Ban User</h2>
                <p>Are you sure you want to ban this user? This action is permanent and cannot be
                  reversed.</p>
              </div>
              <div class="mb-8">
                <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Ban</label>
                <input type="text" wire:model="reason" id="reason"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('reason')
                  <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
              </div>
              <div class="flex justify-end gap-2">
                <button type="button" wire:click="banUser"
                  class="flex flex-row gap-2 items-center px-4 py-2 bg-accent-red/10 text-accent-red hover:bg-accent-red hover:text-white rounded">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-ban">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M4.929 4.929 19.07 19.071" />
                  </svg>
                  Ban User</button>
                <button type="button" wire:click="closeModal"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      @endif

      {{-- Unsuspend Modal --}}
      @if ($showUnsuspendModal)
        <div x-data x-cloak x-show="$wire.showUnsuspendModal"
          @keydown.escape.window="$wire.showUnsuspendModal = false">
          <div class="fixed inset-0 bg-black/50 z-40"></div>
          <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="bg-canvas border border-hairline rounded-lg shadow-lg p-6 w-full max-w-md">
              <div class="flex flex-col gap-2 mb-4">
                <h2 class="text-lg font-semibold">Unsuspend User</h2>
                <p>Are you sure you want to unsuspend this user? This action will restore their access.</p>
              </div>
              <div class="flex justify-end gap-2">
                <button type="button" wire:click="UnsuspendUser"
                  class="flex flex-row gap-2 items-center px-4 py-2 bg-accent-yellow/10 text-accent-yellow hover:bg-accent-yellow hover:text-white rounded">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-circle-alert">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" x2="12" y1="8" y2="12" />
                    <line x1="12" x2="12.01" y1="16" y2="16" />
                  </svg>
                  Unsuspend User</button>
                <button type="button" wire:click="closeModal"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      @endif

      {{-- Unban Modal --}}
      @if ($showUnbanModal)
        <div x-data x-cloak x-show="$wire.showUnbanModal" @keydown.escape.window="$wire.showUnbanModal = false">
          <div class="fixed inset-0 bg-black/50 z-40"></div>
          <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="bg-canvas border border-hairline rounded-lg shadow-lg p-6 w-full max-w-md">
              <div class="flex flex-col gap-2 mb-4">
                <h2 class="text-lg font-semibold">Unban User</h2>
                <p>Are you sure you want to unban this user? The ban will be lifted, and his account will be reactivated.</p>
              </div>
              <div class="flex justify-end gap-2">
                <button type="button" wire:click="unbanUser"
                  class="flex flex-row gap-2 items-center px-4 py-2 bg-accent-red/10 text-accent-red hover:bg-accent-red hover:text-white rounded">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-ban">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M4.929 4.929 19.07 19.071" />
                  </svg>
                  Unban User</button>
                <button type="button" wire:click="closeModal"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      @endif

</div>
</section>
</main>
</div>
