@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="flex flex-col w-full h-max">

    <h1 class="text-ink text-heading-2 font-bold mb-4">Settings</h1>
    <div class="settings-list">
        <div class="settings-group">
          <a href="{{ route('settings.profile') }}" class="settings-card">
            <div class="card-left">
              <i class="fa-regular fa-user"></i>
              <span>Profile</span>
            </div>
            <i class="fa-solid fa-play arrow-icon"></i>
          </a>

          <a href="{{ route('settings.account') }}" class="settings-card">
            <div class="card-left">
              <i class="fa-solid fa-lock"></i>
              <span>Account</span>
            </div>
            <i class="fa-solid fa-play arrow-icon"></i>
          </a>
        </div>

        <form method="POST" action="{{ route('logout') }}" id="logout-form" class="settings-group">
          @csrf
          <button type="button" class="settings-card logout-card" id="logout-btn">
            <div class="card-left">
              <i class="fa-solid fa-arrow-right-from-bracket"></i>
              <span>Logout</span>
            </div>
          </button>
        </form>
    </div>
  </div>

  <div class="logout-modal-overlay" id="logout-modal">

    <div class="logout-modal">
      <div class="flex flex-col gap-4 items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none"
          stroke="#ff383c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-triangle-alert-icon lucide-triangle-alert">
          <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" />
          <path d="M12 9v4" />
          <path d="M12 17h.01" />
        </svg>
        <h3>Are you sure want to logout?</h3>
      </div>

      <div class="logout-modal-buttons">
        <button type="button" class="logout-yes" id="logout-yes">
          YES
        </button>

        <button type="button" class="logout-cancel" id="logout-cancel">
          Cancel
        </button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('turbo:load', function() {

      const logoutBtn = document.getElementById('logout-btn');
      const logoutModal = document.getElementById('logout-modal');
      const logoutYes = document.getElementById('logout-yes');
      const logoutCancel = document.getElementById('logout-cancel');
      const logoutForm = document.getElementById('logout-form');

      logoutBtn.addEventListener('click', function() {
        logoutModal.classList.add('show');
      });

      logoutCancel.addEventListener('click', function() {
        logoutModal.classList.remove('show');
      });

      logoutYes.addEventListener('click', function() {
        logoutForm.submit();
      });

      logoutModal.addEventListener('click', function(event) {
        if (event.target === logoutModal) {
          logoutModal.classList.remove('show');
        }
      });

    });
  </script>
@endsection
