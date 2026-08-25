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
    
  </main>
  @stack('scripts')
</body>

</html>
