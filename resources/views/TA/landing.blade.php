@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
@endsection

@section('content')
<div class="landing">
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-text">
                <h1>Every Hobby<br>Has an Orbit</h1>
                <p>Every hobby has its own community. Find your niche, and find people who share your passion.</p>
                <a href="{{ route('login') }}" class="btn">Join Now →</a>

                <div class="stats">
                    <div><strong>12K+</strong><span>Active Members</span></div>
                    <div><strong>30+</strong><span>Hobby Categories</span></div>
                    <div><strong>80+</strong><span>Clubs</span></div>
                    <div><strong>25K+</strong><span>Posts</span></div>
                </div>
            </div>

            <div class="hero-image">
                <img src="{{ asset('image/rafiki.png') }}" alt="Orbii Community">
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="title">
                <h2>Why Choose Orbii</h2>
                <p>Alasan mereka memilih Orbii untuk komunitas hobi mereka</p>
            </div>

            <div class="grid features-small">
                <div class="card">
                    <i class="bi bi-person-plus icon"></i>
                    <h3>Easy to Join</h3>
                    <p>Temukan dan bergabung dengan klub hobi favoritmu hanya dalam beberapa langkah sederhana.</p>
                </div>
                <div class="card">
                    <i class="bi bi-shield-check icon"></i>
                    <h3>Safe Community</h3>
                    <p>Sistem moderasi, laporan, suspend, dan banned membantu menjaga komunitas tetap aman dan nyaman.</p>
                </div>
                <div class="card">
                    <i class="bi bi-stars icon"></i>
                    <h3>Personalized Clubs</h3>
                    <p>Dapatkan rekomendasi klub berdasarkan minat dan hobi yang kamu pilih saat mendaftar.</p>
                </div>
                <div class="card">
                    <i class="bi bi-chat-square-text icon"></i>
                    <h3>Real-time Discussion</h3>
                    <p>Berdiskusi melalui forum, komentar, maupun pesan langsung dengan anggota klub kapan saja.</p>
                </div>
                <div class="card">
                    <i class="bi bi-folder2-open icon"></i>
                    <h3>Share Files Easily</h3>
                    <p>Bagikan foto, dokumen, dan berbagai berkas penting klub secara terpusat dan mudah diakses.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="title">
                <h2>How it Works</h2>
                <p>5 langkah mudah untuk mulai bergabung</p>
            </div>

            <div class="steps">
                <div class="step">
                    <b>1</b>
                    <div>
                        <h3>Create</h3>
                        <p>Daftar akun menggunakan email atau akun Google, lalu mulai menjelajahi komunitas hobi.</p>
                    </div>
                </div>
                <div class="step">
                    <b>2</b>
                    <div>
                        <h3>Complete Your Profile</h3>
                        <p>Tambahkan foto, bio, dan minat agar anggota lain lebih mudah mengenalmu.</p>
                    </div>
                </div>
                <div class="step">
                    <b>3</b>
                    <div>
                        <h3>Choose Your Interests</h3>
                        <p>Pilih hobi yang kamu sukai untuk mendapatkan rekomendasi klub yang sesuai.</p>
                    </div>
                </div>
                <div class="step">
                    <b>4</b>
                    <div>
                        <h3>Join Your Favorite Club</h3>
                        <p>Bergabung dengan klub dan mulai menjadi bagian dari komunitas yang sesuai minatmu.</p>
                    </div>
                </div>
                <div class="step">
                    <b>5</b>
                    <div>
                        <h3>Connect & Share</h3>
                        <p>Ikuti diskusi, bagikan pengalaman, dan bangun koneksi dengan anggota lainnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="title">
                <h2>Main Features</h2>
                <p>Semua fitur utama Orbii, dari diskusi hingga moderasi</p>
            </div>

            <div class="feature-list">
                <div class="card feature">
                    <div>
                        <h3>Forum Discussion</h3>
                        <p>Diskusikan ide, ajukan pertanyaan, dan berbagi pengalaman bersama anggota klub dalam forum yang terorganisir.</p>
                    </div>
                    <img src="{{ asset('image/rafiki.png') }}" alt="Forum Discussion">
                </div>
                <div class="card feature reverse">
                    
                    <div>
                        <h3>Create & Share Posts</h3>
                        <p>Bagikan cerita, tips, dokumentasi kegiatan, atau informasi terbaru kepada seluruh anggota klub.</p>
                    </div>
                    <img src="{{ asset('image/rafiki.png') }}" alt="Create and Share Posts">
                </div>
                <div class="card feature">
                    <div>
                        <h3>Official Club Announcements</h3>
                        <p>Pengurus klub dapat menyampaikan pengumuman penting agar seluruh anggota mendapatkan informasi terbaru.</p>
                    </div>
                    <img src="{{ asset('image/rafiki.png') }}" alt="Club Announcements">
                </div>
                <div class="card feature reverse">
                    <div>
                        <h3>Interactive Comments</h3>
                        <p>Berikan tanggapan, diskusikan ide, dan bangun percakapan langsung pada setiap postingan.</p>
                    </div>
                    <img src="{{ asset('image/rafiki.png') }}" alt="Interactive Comments">
                </div>
                <div class="card feature">
                    <div>
                        <h3>Direct Messaging</h3>
                        <p>Hubungi anggota lain secara pribadi untuk berdiskusi, bertanya, atau membangun relasi baru.</p>
                    </div>
                    <img src="{{ asset('image/rafiki.png') }}" alt="Direct Messaging">
                </div>
                <div class="card feature reverse">
                    <div>
                        <h3>Safe Community</h3>
                        <p>Laporkan konten atau pengguna yang melanggar aturan agar komunitas tetap nyaman dan aman.</p>
                    </div>
                    <img src="{{ asset('image/rafiki.png') }}" alt="Safe Community">
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="title">
                <h2>Explore Hobbies</h2>
                <p>Temukan komunitas berdasarkan hobi yang kamu sukai</p>
            </div>

            <div class="grid hobbies">
                <a href="#" class="card hobby">
                    <i class="bi bi-music-note-beamed icon"></i>
                    <strong>Music</strong>
                    <small>769 Members</small>
                </a>
                <a href="#" class="card hobby">
                    <i class="bi bi-mountain icon"></i>
                    <strong>Hiking</strong>
                    <small>349 Members</small>
                </a>
                <a href="#" class="card hobby">
                    <i class="bi bi-water icon"></i>
                    <strong>Fishing</strong>
                    <small>1.267 Members</small>
                </a>
                <a href="#" class="card hobby">
                    <i class="bi bi-controller icon"></i>
                    <strong>Gaming</strong>
                    <small>2.654 Members</small>
                </a>
                <a href="#" class="card hobby">
                    <i class="bi bi-dribbble icon"></i>
                    <strong>Football</strong>
                    <small>1.680 Members</small>
                </a>
                <a href="#" class="card hobby">
                    <i class="bi bi-book icon"></i>
                    <strong>Reading</strong>
                    <small>873 Members</small>
                </a>
                <a href="#" class="card hobby">
                    <i class="bi bi-airplane icon"></i>
                    <strong>Traveling</strong>
                    <small>950 Members</small>
                </a>
                <a href="#" class="card hobby">
                    <i class="bi bi-water icon"></i>
                    <strong>Swimming</strong>
                    <small>869 Members</small>
                </a>
                <a href="#" class="card hobby">
                    <i class="bi bi-camera icon"></i>
                    <strong>Photography</strong>
                    <small>794 Members</small>
                </a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="title">
                <h2>Community Showcase</h2>
                <p>Klub-klub populer yang sedang aktif</p>
            </div>

            <div class="grid communities">
                <div class="card community">
                    <div class="community-image">
                        <img src="{{ asset('image/rafiki.png') }}" alt="Street Photography">
                        <span>Popular</span>
                    </div>
                    <div>
                        <h3>Street Photography</h3>
                        <p>Photography · 456 anggota</p>
                        <a href="#" class="join">Join →</a>
                    </div>
                </div>
                <div class="card community">
                    <div class="community-image">
                        <img src="{{ asset('image/rafiki.png') }}" alt="Fishing">
                        <span>Popular</span>
                    </div>
                    <div>
                        <h3>Fishing</h3>
                        <p>Fishing · 926 anggota</p>
                        <a href="#" class="join">Join →</a>
                    </div>
                </div>
                <div class="card community">
                    <div class="community-image">
                        <img src="{{ asset('image/rafiki.png') }}" alt="Reading">
                        <span>Popular</span>
                    </div>
                    <div>
                        <h3>Reading</h3>
                        <p>Reading · 531 anggota</p>
                        <a href="#" class="join">Join →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <div class="cta-card">
                <h2>Temukan Komunitas yang Sesuai dengan Hobimu.</h2>
                <p>Bergabung dengan berbagai klub hobi, temukan teman baru, dan mulai berbagi pengalaman bersama komunitas yang aktif.</p>
                <div>
                    <a href="{{ route('login') }}" class="btn outline">Login</a>
                    <a href="{{ route('register') }}" class="btn">Join Now →</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
