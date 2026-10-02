@extends('layouts.app')

@section('title', 'orbii | Verify Email')

@section('content')
  <div class="flex flex-col gap-4 max-w-md lg:max-w-lg p-6 rounded-xl border border-hairline shadow-md">
    <img src="{{ asset('images/email.png') }}" alt="Email" class="w-max h-32 lg:h-64 mx-auto">
    <h1 class="text-title text-center lg:text-2xl font-semibold text-primary">Verifikasi Email</h1>
    <p class="text-caption text-ink-muted">Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email
      Anda dengan mengklik tautan yang telah kami kirimkan. Jika Anda tidak menerima email, kami akan dengan senang hati
      mengirimkan yang baru.</p>

    @if (session('status') === 'verification-link-sent')
      <div class="rounded-lg bg-success/20 p-4 text-sm text-primary border border-accent-sky">
        Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
      </div>
    @endif

    @error('resend')
      <p class="text-sm text-accent-red">{{ $message }}</p>
    @enderror

    <div class="mt-6 flex items-center justify-between">
      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" id="resend-btn" data-seconds="{{ $remaining ?? 0 }}"
          class="rounded-md bg-primary px-4 py-2 text-caption font-semibold text-white shadow-sm hover:bg-primary/80 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
          Kirim Ulang Email
        </button>
      </form>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
          class="text-accent-red text-caption cursor-pointer font-semibold hover:underline focus:outline-none focus:ring-2 focus:ring-accent-red focus:ring-offset-2">
          Keluar
        </button>
      </form>
    </div>
  </div>

  <script>
    (function() {
      const btn = document.getElementById('resend-btn');
      if (!btn) return;

      const label = 'Kirim Ulang Email';
      let seconds = parseInt(btn.dataset.seconds, 10) || 0;

      function render() {
        if (seconds > 0) {
          btn.disabled = true;
          btn.textContent = `Kirim ulang dalam ${seconds}s`;
        } else {
          btn.disabled = false;
          btn.textContent = label;
        }
      }

      render();
      if (seconds <= 0) return;

      const timer = setInterval(() => {
        if (!document.body.contains(btn)) return clearInterval(timer);

        seconds--;
        render();
        if (seconds <= 0) clearInterval(timer);
      }, 1000);
    })();
  </script>
@endsection
