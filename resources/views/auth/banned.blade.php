<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Appeals</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-canvas-soft">
  <x-alert />
  <main class="w-full flex flex-col gap-5 lg:flex-row justify-center items-center rounded-lg h-dvh overflow-hidden">
    <div class="flex order-1 md:max-w-100 justify-center w-max p-3 lg:border-r lg:border-hairline">
      <img src="{{ asset('images/illustration/warning.svg') }}" alt="Login Illustration"
        class="w-37.5 md:w-full max-w-50">
    </div>

    <div class="order-2 md:w-1/2 max-w-200 w-full p-3 flex flex-col">
      <p class="text-body-mid text-ink">Akun Anda telah diblokir. Anda dapat mengajukan banding untuk meminta agar
        akun Anda diaktifkan kembali.</p>
      <span><a href="{{ route('appeal.create') }}" class="text-primary underline">Ajukan
          banding</a>.</span>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <x-secondary-button type="submit" class="mt-4 w-max">
          {{ __('Logout') }}
        </x-secondary-button>
      </form>
    </div>
  </main>
  @stack('scripts')
</body>

</html>
