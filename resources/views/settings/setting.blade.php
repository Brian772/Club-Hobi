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
          {{-- Profile Card --}}
          <a href="{{ route('settings.profile') }}" class="settings-card">
            <div class="card-left">
              <i class="fa-regular fa-user"></i>
              <span>Profile</span>
            </div>
            <i class="fa-solid fa-play arrow-icon"></i>
          </a>

          {{-- Account Card --}}
          <a href="{{ route('settings.account') }}" class="settings-card">
            <div class="card-left">
              <i class="fa-solid fa-lock"></i>
              <span>Account</span>
            </div>
            <i class="fa-solid fa-play arrow-icon"></i>
          </a>
        </div>

        {{-- Logout Card --}}
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

  {{-- Logout Modal --}}
  <div class="logout-modal-overlay" id="logout-modal">

    <div class="logout-modal">
      <h3>Are You sure Want To Logout?</h3>

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
