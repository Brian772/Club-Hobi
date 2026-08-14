@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="mobile-screen dashboard-mobile">
    <aside class="mobile-sidebar" id="mobileSidebar">
        <button type="button" class="sidebar-close" onclick="document.getElementById('mobileSidebar').classList.remove('open'); document.getElementById('mobileSidebarOverlay').classList.remove('show')">
            <i class="bi bi-x"></i>
        </button>

        <nav class="sidebar-nav">
            <a href="#" class="sidebar-link active">
                <span class="sidebar-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                <span>dashboard</span>
            </a>
            <a href="#" class="sidebar-link">
                <span class="sidebar-icon"><i class="bi bi-bell"></i></span>
                <span>Notifikasi</span>
            </a>
            <a href="#" class="sidebar-link">
                <span class="sidebar-icon"><i class="bi bi-chat-left-text"></i></span>
                <span>Pesan</span>
            </a>
            <a href="#" class="sidebar-link">
                <span class="sidebar-icon"><i class="bi bi-people"></i></span>
                <span>Club</span>
            </a>
            <a href="#" class="sidebar-link">
                <span class="sidebar-icon"><i class="bi bi-gear"></i></span>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <div class="mobile-sidebar-overlay" id="mobileSidebarOverlay" onclick="document.getElementById('mobileSidebar').classList.remove('open'); this.classList.remove('show')"></div>

    <header class="top-bar dashboard-topbar">
        <button type="button" class="icon-btn menu-trigger" onclick="document.getElementById('mobileSidebar').classList.toggle('open'); document.getElementById('mobileSidebarOverlay').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>
        <div class="profile-badge">J</div>
    </header>

    <main class="dashboard-content">
        <div class="dashboard-header-row">
            <h1>Halo, Jhon <span>👋</span></h1>
            <button type="button" class="header-plus-btn"><i class="bi bi-plus-lg"></i></button>
        </div>

        <div class="dashboard-subheader">
            <span>hobi yang anda minati</span>
            <a href="#">Lihat selengkapnya →</a>
        </div>

        <div class="stats-row dashboard-stats">
            <article class="mini-card">
                <div class="mini-card-image grayscale"></div>
                <h3>Street Photography</h3>
                <p>Photography <span>· 459 anggota</span></p>
                <small>3 postingan baru</small>
            </article>

            <article class="mini-card">
                <div class="mini-card-image fishing"></div>
                <h3>Fishing</h3>
                <p>Fishing <span>· 926 anggota</span></p>
                <small>1 pengumuman baru</small>
            </article>
        </div>

        <section class="feed-card">
            <article class="feed-post">
                <div class="feed-user">
                    <div class="user-avatar">R</div>
                    <div class="feed-meta">
                        <strong>Rangga P.</strong>
                        <small>memposting di klub fotografi · 10 menit lalu</small>
                    </div>
                </div>

                <p>photo shoot minggu ini seru banget</p>

                <div class="feed-image landscape"></div>

                <div class="feed-actions">
                    <span><i class="bi bi-chat"></i> 24</span>
                    <span><i class="bi bi-heart"></i> 8 comment</span>
                </div>
            </article>

            <article class="feed-post secondary-post">
                <div class="feed-user">
                    <div class="user-avatar">S</div>
                    <div class="feed-meta">
                        <strong>Sinta W.</strong>
                        <small>memposting di klub musik · 1 jam lalu</small>
                    </div>
                </div>

                <p>guys, ada yang udah dengar lagu terbarunya Tulus belum?</p>

                <div class="feed-actions">
                    <span><i class="bi bi-chat"></i> 47</span>
                    <span><i class="bi bi-heart"></i> 28 comment</span>
                </div>
            </article>
        </section>
    </main>
</div>
@endsection