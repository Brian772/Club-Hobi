<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Hobi</title>
    @yield('styles')
</head>
<body>
    <div class="app-shell">
        @if (session('success'))
            <div class="alert alert-success" style="max-width:1200px; margin:1rem auto 0; background:#d1fae5; color:#065f46; border-radius:12px; padding:0.8rem 1rem;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error" style="max-width:1200px; margin:1rem auto 0; background:#fee2e2; color:#991b1b; border-radius:12px; padding:0.8rem 1rem;">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>
