@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
<div class="dashboard-shell">
    <aside class="dashboard-sidebar">
        <div class="brand-box">
            <span class="orbii-mark">◉</span>
            <span>orbii</span>
        </div>

        <nav class="side-nav">
            <a href="#" class="side-item active">
                <span class="side-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                <span>Dashboard</span>
            </a>
            <a href="#" class="side-item">
                <span class="side-icon"><i class="bi bi-bell"></i></span>
                <span>Notifikasi</span>
            </a>
            <a href="#" class="side-item">
                <span class="side-icon"><i class="bi bi-chat-left-text"></i></span>
                <span>Pesan</span>
            </a>
            <a href="#" class="side-item">
                <span class="side-icon"><i class="bi bi-people"></i></span>
                <span>Club</span>
            </a>
            <a href="#" class="side-item">
                <span class="side-icon"><i class="bi bi-gear"></i></span>
                <span>Settings</span>
            </a>
        </nav>

        <div class="user-mini">
            <div class="user-avatar">J</div>
            <div>
                <strong>Jhon</strong>
            </div>
        </div>
    </aside>

    <main class="dashboard-main">
        <div class="dashboard-header">
            <h1>Halo, Jhon <span>👋</span></h1>
            <button type="button" class="mini-add-btn"><i class="bi bi-plus-lg"></i></button>
        </div>

        <div class="section-label-row">
            <span>hobi yang anda minati</span>
            <a href="#">Lihat selengkapnya →</a>
        </div>

        <div class="interest-grid">
            <article class="interest-card photo-card">
                <div class="interest-image"></div>
                <h3>Street Photography</h3>
                <p>Photography <span>· 459 anggota</span></p>
                <small>3 postingan baru</small>
            </article>

            <article class="interest-card fishing-card">
                <div class="interest-image"></div>
                <h3>Fishing</h3>
                <p>Fishing <span>· 926 anggota</span></p>
                <small>1 pengumuman baru</small>
            </article>

            <article class="interest-card reading-card">
                <div class="interest-image"></div>
                <h3>Reading</h3>
                <p>Reading <span>· 531 anggota</span></p>
                <small>4 postingan baru</small>
            </article>
        </div>

        <section class="post-list">
            <article class="post-item">
                <div class="post-head">
                    <div class="user-avatar small">R</div>
                    <div class="post-meta">
                        <strong>Rangga P.</strong>
                        <small>memposting di klub fotografi · 10 menit lalu</small>
                    </div>
                </div>

                <p>photo shoot minggu ini seru banget</p>

                <div class="post-image scenic-image"></div>

                <div class="post-actions">
                    <span><i class="bi bi-chat"></i> 24</span>
                    <span><i class="bi bi-chat-left-text"></i> 8 comment</span>
                </div>
            </article>

            <article class="post-item muted-post">
                <div class="post-head">
                    <div class="user-avatar small alt">S</div>
                    <div class="post-meta">
                        <strong>Sinta W.</strong>
                        <small>memposting di klub musik · 1 jam lalu</small>
                    </div>
                </div>

                <p>guys, ada yang udah dengar lagu terbarunya Tulus belum?</p>

                <div class="post-actions">
                    <span><i class="bi bi-chat"></i> 47</span>
                    <span><i class="bi bi-chat-left-text"></i> 28 comment</span>
                </div>
            </article>
        </section>
    </main>

    <aside class="dashboard-rightbar">
        <div class="rightbar-top">
            <button type="button" class="close-mini">×</button>
        </div>

        <div class="notif-box">
            <div class="notif-header">
                <h3>Notifikasi</h3>
                <a href="#">See More →</a>
            </div>

            <div class="notif-list">
                <div class="notif-item">
                    <span class="notif-icon"><i class="bi bi-bell"></i></span>
                    <div>
                        <strong>Rangga P.</strong>
                        <small>memposting kanal baru</small>
                    </div>
                </div>

                <div class="notif-item">
                    <span class="notif-icon"><i class="bi bi-exclamation-circle"></i></span>
                    <div>
                        <strong>Hasil review</strong>
                        <small>Komunitas <b>Club ID</b> baru saja diupdate</small>
                    </div>
                </div>

                <div class="notif-item">
                    <span class="notif-icon"><i class="bi bi-chat-left-text"></i></span>
                    <div>
                        <strong>Sinta W.</strong>
                        <small>Telah membalas komentar Anda</small>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>
@endsection