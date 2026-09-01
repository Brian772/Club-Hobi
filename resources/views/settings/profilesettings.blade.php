@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/settings.css') }}">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection


@section('content')

  <div class="app-layout">
    <div class="flex flex-col w-full">
      <div class="page-header">
        <a href="{{ route('settings.index') }}" class="back-link">

          <svg class="back-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>

          <h1 class="page-titleP">Profile</h1>
        </a>
      </div>

      <form action="{{ route('settings.profile.update') }}" method="POST" class="profile-page-content w">
        @csrf
        <div class="profile-avatar-section">
          <div class="profile-avatar-lg">
            @if ($user->avatar_url)
              <img src="{{ asset('storage/' . $user->avatar_url) }}" alt="Foto Profil">
            @else
              {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
          </div>

          <div class="profile-avatar-info">
            <button type="button" class="btn-edit-photo" onclick="openAvatarModal()">
              <i class="fa-solid fa-pencil"></i>
              Edit foto
            </button>

            <span class="photo-hint">JPG/PNG, max 2MB</span>
          </div>
        </div>

        <div class="form-group-item">
          <label class="input-label">Nama</label>

          <input type="text" name="name"
            class="rounded-full border border-hairline focus-within:border-blue-500 focus-within:ring focus-within:ring-blue-200"
            value="{{ old('name', $user->name) }}" maxlength="255" required>
        </div>

        <div class="form-group-item">
          <label class="input-label">Bio</label>

          <div class="settings-group bio-group">
            <textarea name="bio" class="custom-textarea" rows="3" maxlength="150" id="bioInput">{{ old('bio', $user->bio) }}</textarea>
          </div>

          <div class="char-counter">
            <span id="bioCounter">{{ strlen($user->bio ?? '') }}</span>/150
          </div>
        </div>

        <div class="form-group-item">
          <label class="input-label">Hobi</label>

          <div class="hobby-list" id="hobbyList">
            @forelse ($user->clubs as $club)
              <span class="hobby-badge active select-none cursor-pointer" data-club-id="{{ $club->id }}"
                onclick="openDeleteHobbyModal(this)">{{ $club->category }}</span>
            @empty
              <span class="empty-hobby" id="emptyHobby">Belum ada hobi</span>
            @endforelse

            <button type="button" class="btn-add-hobby" onclick="openHobbyModal()">
              <i class="fa-solid fa-plus"></i>
              Tambah
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  @if (session('success'))
    <div class="profile-toast success-toast" id="successToast">
      <i class="fa-solid fa-circle-check"></i>
      <span>{{ session('success') }}</span>

      <button type="button" class="toast-close" onclick="closeToast('successToast')">
        &times;
      </button>
    </div>
  @endif

  @php
      $profileErrors = $errors ?? collect();
      $profileErrorMessages = is_object($profileErrors) && method_exists($profileErrors, 'all') ? $profileErrors->all() : [];
  @endphp

  @if (!empty($profileErrorMessages))
    <div class="profile-toast error-toast" id="errorToast">
      <i class="fa-solid fa-circle-exclamation"></i>
      <div class="toast-error-content">
        @foreach ($profileErrorMessages as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>

      <button type="button" class="toast-close" onclick="closeToast('errorToast')">
        &times;
      </button>
    </div>
  @endif

  <div class="profile-modal-overlay" id="avatarModal">
    <div class="profile-modal">
      <div class="modal-header">
        <h3>Foto Profil</h3>

        <button type="button" class="modal-close" onclick="closeAvatarModal()">
          &times;
        </button>
      </div>

      <div class="modal-body">
        <div class="avatar-preview">
          @if ($user->avatar_url)
            <img src="{{ asset('storage/' . $user->avatar_url) }}" alt="Foto Profil">
          @else
            <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
          @endif
        </div>

        <form action="{{ route('settings.profile.avatar') }}" method="POST" enctype="multipart/form-data"
          class="avatar-upload-form">

          @csrf
          <input type="file" name="avatar" id="avatarInput" accept=".jpg,.jpeg,.png" required
            onchange="showSelectedFile(this)">
          <label for="avatarInput" class="avatar-select-button">
            <i class="fa-solid fa-image"></i>
            Pilih Foto
          </label>

          <span class="selected-file-name" id="selectedFileName">Belum ada foto dipilih</span>

          <button type="submit" class="btn-modal-primary">Ganti Foto</button>
        </form>

        @if ($user->avatar_url)
          <form action="{{ route('settings.profile.avatar.delete') }}" method="POST" class="avatar-delete-form">

            @csrf
            @method('DELETE')
            <button type="submit" class="btn-modal-danger"
              onclick="return confirm('Yakin ingin menghapus foto profil?')">
              <i class="fa-solid fa-trash"></i>
              Hapus Foto
            </button>
          </form>
        @endif
      </div>
    </div>
  </div>

  <div class="profile-modal-overlay" id="hobbyModal">
    <div class="profile-modal hobby-modal">
      <div class="modal-header">
        <h3>Tambah Hobi</h3>

        <button type="button" class="modal-close" onclick="closeHobbyModal()">
          &times;
        </button>
      </div>

      <div class="modal-body">
        <p class="modal-description">
          Pilih salah satu hobi yang tersedia.
        </p>

        <form action="{{ route('settings.profile.hobby.add') }}" method="POST" id="hobbyForm">
          @csrf
          {{-- Hidden input.
                     Tidak ada checkbox/radio yang terlihat. --}}
          <input type="hidden" name="club_id" id="selectedClubId" value="">

          <div class="hobby-options">
            @forelse ($clubs as $club)
              @php
                $alreadyJoined = $user->clubs->contains('category', $club->category);
              @endphp

              <button type="button" class="hobby-card {{ $alreadyJoined ? 'disabled' : '' }}"
                data-club-id="{{ $club->id }}" {{ $alreadyJoined ? 'disabled' : '' }}onclick="selectHobby(this)">

                <div class="hobby-card-content">
                  <strong class="hobby-title">{{ $club->category }}</strong>
                </div>

                @if ($alreadyJoined)
                  <span class="already-added">Sudah dipilih</span>
                @else
                  <span class="hobby-check">
                    <i class="fa-solid fa-check"></i>
                  </span>
                @endif
              </button>

            @empty
              <div class="empty-hobby-database">
                Belum ada data club/hobi
                di database
              </div>
            @endforelse
          </div>

          @if ($clubs->whereNotIn('category', $user->clubs->pluck('category'))->count() > 0)
            <button type="submit" class="btn-modal-primary" id="addHobbyButton" disabled>
              <i class="fa-solid fa-plus"></i>
              Tambah Hobi
            </button>
          @endif
        </form>
      </div>
    </div>
  </div>

  <!-- POPUP HAPUS HOBI -->
  <!-- Modal Hapus Akun sekarang berada di halaman Account (accountsettings.blade.php) -->
  <div id="deleteHobbyModal" class="delete-hobby-popover">
    <div class="delete-hobby-content" onclick="confirmDeleteHobby(event)">
      <i class="fa-solid fa-trash"></i>
      <span>Hapus Hobi</span>
    </div>
  </div>

  <script>
    function openAvatarModal() {
      document
        .getElementById('avatarModal')
        .classList.add('show');
    }

    function closeAvatarModal() {
      document
        .getElementById('avatarModal')
        .classList.remove('show');
    }

    function openHobbyModal() {
      document
        .getElementById('hobbyModal')
        .classList.add('show');
    }

    function closeHobbyModal() {
      document
        .getElementById('hobbyModal')
        .classList.remove('show');
      resetHobbySelection();
    }

    function selectHobby(element) {
      const clubId = element.dataset.clubId;
      const hiddenInput =
        document.getElementById('selectedClubId');
      const addButton =
        document.getElementById('addHobbyButton');
      document
        .querySelectorAll('.hobby-card.selected')
        .forEach(function(card) {
          card.classList.remove('selected');
        });
      element.classList.add('selected');

      hiddenInput.value = clubId;

      if (addButton) {
        addButton.disabled = false;
      }
    }

    function resetHobbySelection() {
      const hiddenInput =
        document.getElementById('selectedClubId');
      const addButton =
        document.getElementById('addHobbyButton');
      if (hiddenInput) {
        hiddenInput.value = '';
      }

      document
        .querySelectorAll('.hobby-card.selected')
        .forEach(function(card) {
          card.classList.remove('selected');
        });

      if (addButton) {
        addButton.disabled = true;
      }
    }

    function showSelectedFile(input) {
      const fileName =
        document.getElementById('selectedFileName');
      if (
        input.files &&
        input.files.length > 0
      ) {
        fileName.textContent =
          input.files[0].name;
      } else {
        fileName.textContent =
          'Belum ada foto dipilih';
      }
    }

    function closeToast(id) {
      const toast =
        document.getElementById(id);
      if (!toast) {
        return;
      }
      toast.classList.add('hide');
      setTimeout(function() {
        if (toast) {
          toast.remove();
        }
      }, 250);
    }

    document.addEventListener(
      'click',
      function(event) {
        const avatarModal =
          document.getElementById('avatarModal');
        const hobbyModal =
          document.getElementById('hobbyModal');

        if (event.target === avatarModal) {
          closeAvatarModal();
        }

        if (event.target === hobbyModal) {
          closeHobbyModal();
        }
      }
    );

    const nameInput = document.querySelector('input[name="name"]');
    const bioInput = document.getElementById('bioInput');
    const bioCounter = document.getElementById('bioCounter');

    let saveTimer;

    function autoSaveProfile() {
      clearTimeout(saveTimer);

      saveTimer = setTimeout(function() {

        const name = nameInput.value.trim();
        const bio = bioInput.value;

        if (name === '') {
          return;
        }

        fetch("{{ route('settings.profile.update') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
              name: name,
              bio: bio
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              console.log('Profile berhasil disimpan.');
            }
          })
          .catch(error => {
            console.error('Gagal menyimpan profile:', error);
          });

      }, 800);
    }


    /* =========================
       AUTO SAVE NAMA
    ========================= */

    if (nameInput) {
      nameInput.addEventListener('input', function() {
        autoSaveProfile();
      });
    }


    /* =========================
       AUTO SAVE BIO
    ========================= */

    if (bioInput) {
      bioInput.addEventListener('input', function() {

        if (bioCounter) {
          bioCounter.textContent = this.value.length;
        }

        autoSaveProfile();
      });
    }

    let selectedHobbyElement = null;
    let selectedHobbyId = null;


    /* =========================
       BUKA MODAL HAPUS HOBI
    ========================= */

    function openDeleteHobbyModal(element) {

      selectedHobbyElement = element;
      selectedHobbyId = element.dataset.clubId;

      const modal = document.getElementById('deleteHobbyModal');

      modal.classList.add('show');

      /*
       * Ambil posisi badge hobi yang diklik
       */
      const rect = element.getBoundingClientRect();

      /*
       * Reset posisi dulu supaya offsetWidth/offsetHeight
       * dihitung dengan benar
       */
      modal.style.left = '0px';
      modal.style.top = '0px';

      const modalWidth = modal.offsetWidth;
      const modalHeight = modal.offsetHeight;

      /*
       * Posisi default: tepat di atas badge
       */
      let left = rect.left + (rect.width / 2) - (modalWidth / 2);
      let top = rect.top - modalHeight - 8;

      /*
       * Jangan sampai keluar layar sebelah kiri
       */
      if (left < 8) {
        left = 8;
      }

      /*
       * Jangan sampai keluar layar sebelah kanan
       */
      if (left + modalWidth > window.innerWidth - 8) {
        left = window.innerWidth - modalWidth - 8;
      }

      if (top < 8) {
        top = rect.bottom + 8;
      }

      modal.style.left = `${left}px`;
      modal.style.top = `${top}px`;
    }

    function closeDeleteHobbyModal(event) {

      if (event) {
        event.stopPropagation();
      }

      document
        .getElementById('deleteHobbyModal')
        .classList.remove('show');

      selectedHobbyElement = null;
      selectedHobbyId = null;
    }

    function confirmDeleteHobby(event) {

      if (event) {
        event.stopPropagation();
      }

      if (!selectedHobbyId || !selectedHobbyElement) {
        return;
      }

      const hobbyElement = selectedHobbyElement;
      const hobbyId = selectedHobbyId;

      fetch(`/settings/profile/hobby/${hobbyId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        })
        .then(response => response.json())
        .then(data => {

          if (data.success) {

            hobbyElement.remove();

            closeDeleteHobbyModal();

            /* Jika sudah tidak ada hobi */
            const hobbyList =
              document.getElementById('hobbyList');

            const hobbyBadges =
              hobbyList.querySelectorAll('.hobby-badge');

            if (hobbyBadges.length === 0) {

              const emptyHobby =
                document.createElement('span');

              emptyHobby.className = 'empty-hobby';
              emptyHobby.id = 'emptyHobby';
              emptyHobby.textContent = 'Belum ada hobi';

              const addButton =
                hobbyList.querySelector('.btn-add-hobby');

              hobbyList.insertBefore(
                emptyHobby,
                addButton
              );
            }
          }

        })
        .catch(error => {
          console.error('Gagal menghapus hobi:', error);
        });
    }

    document.addEventListener('click', function(event) {

      const deleteHobbyModal =
        document.getElementById('deleteHobbyModal');

      if (
        !deleteHobbyModal ||
        !deleteHobbyModal.classList.contains('show')
      ) {
        return;
      }

      if (
        !deleteHobbyModal.contains(event.target) &&
        !event.target.closest('.hobby-badge')
      ) {
        closeDeleteHobbyModal();
      }
    });

    document.addEventListener(
      'DOMContentLoaded',
      function() {
        const successToast =
          document.getElementById('successToast');
        const errorToast =
          document.getElementById('errorToast');
        if (successToast) {
          setTimeout(function() {
            closeToast('successToast');
          }, 3500);
        }

        if (errorToast) {
          setTimeout(function() {
            closeToast('errorToast');
          }, 5000);
        }
      }
    );
  </script>
@endsection
