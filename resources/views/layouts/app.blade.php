<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Hobi</title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: system-ui, -apple-system, sans-serif; 
            background: #f4f5f7; 
            margin: 0; 
            color: #1f2937; 
            min-height: 100vh;
        }
        
        nav { 
            background: #1f2937; 
            padding: 1rem 2rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        nav a { 
            color: #f9fafb; 
            text-decoration: none; 
            margin-right: 1rem; 
            font-size: 0.95rem; 
        }
        nav a:hover { text-decoration: underline; }
        nav form button { 
            background: #374151; 
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
        }
        
        /* ============================================ */
        /* PERUBAHAN UTAMA: Container menjadi lebih fleksibel */
        /* ============================================ */
        .container { 
            max-width: 2000px; /* Diperbesar dari 720px */
            margin: 0rem auto; 
            background: #fff; 
            padding: 2rem; 
            border-radius: 10px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
        }
        
        /* Class khusus untuk halaman auth (login/register) */
        .auth-page .container {
            max-width: 100%;
            margin: 0;
            padding: 0;
            background: transparent;
            box-shadow: none;
            border-radius: 0;
            min-height: calc(100vh - 80px); /* Kurangi tinggi navbar */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .alert { 
            padding: 0.75rem 1rem; 
            border-radius: 6px; 
            margin-bottom: 1rem; 
            font-size: 0.9rem; 
        }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        
        label { 
            display: block; 
            margin-bottom: 0.3rem; 
            font-weight: 600; 
            font-size: 0.9rem; 
        }
        input, textarea, select { 
            width: 100%; 
            padding: 0.6rem; 
            margin-bottom: 1rem; 
            border: 1px solid #d1d5db; 
            border-radius: 6px; 
            font-size: 0.95rem; 
        }
        button, .btn { 
            display: inline-block; 
            background: #2563eb; 
            color: #fff; 
            border: none; 
            padding: 0.6rem 1.2rem; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 0.95rem; 
            text-decoration: none; 
        }
        button:hover, .btn:hover { background: #1d4ed8; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        
        .error-text { 
            color: #b91c1c; 
            font-size: 0.8rem; 
            margin-top: -0.7rem; 
            margin-bottom: 0.8rem; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 1rem; 
        }
        th, td { 
            text-align: left; 
            padding: 0.6rem; 
            border-bottom: 1px solid #e5e7eb; 
            font-size: 0.9rem; 
        }
        .actions a, .actions button { 
            margin-right: 0.5rem; 
            font-size: 0.85rem; 
        }
    </style>
    @yield('styles')
</head>
<body>
    @auth
    <nav>
        <div>
            <a href="{{ route('profile.index') }}">Profil</a>
            <a href="{{ route('posts.index') }}">Konten</a>
            <a href="{{ route('club_files.index') }}">Dokumentasi</a>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </nav>
    @endauth

    {{-- Tambahkan class auth-page untuk halaman login/register --}}
    <div class="container @if(Route::is('login') || Route::is('register')) auth-page @endif">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>