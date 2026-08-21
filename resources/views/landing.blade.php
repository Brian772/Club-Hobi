@extends('layouts.app')

@php

@endphp

@section('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endsection

@section('content')
  {{-- Hero --}}
  <div class="hidden sm:flex w-full">
    <x-hero-desktop />
  </div>
  <div class="flex sm:hidden w-full">
    <x-hero-mobile />
  </div>

  {{-- Why Choose Us --}}
  <section class="w-full">
    <div class="mb-4">
      <h2 class="text-heading-2 font-bold">Why Choose orbii</h2>
      <p class="text-caption text-ink-muted">Alasan mereka memilih Orbii untuk komunitas hobi mereka</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
      {{-- Community Building --}}
      <div class="bg-canvas rounded-lg p-4 flex flex-col gap-2 border-solid border-hairline border">
        <i class="bi bi-rocket-takeoff text-primary text-heading-2"></i>
        <div class="flex flex-col gap-2 items-start">
          <h3 class="text-title font-bold">Community Building</h3>
          <p class="text-caption text-ink-muted">Fitur-fitur yang memudahkan anggota komunitas untuk berinteraksi dan
            membangun hubungan yang lebih erat.</p>
        </div>
      </div>

      {{-- Safe Community --}}
      <div class="bg-canvas rounded-lg p-4 flex flex-col gap-2 border-solid border-hairline border">
        <i class="bi bi-shield text-primary text-heading-2"></i>
        <div class="flex flex-col gap-2 items-start">
          <h3 class="text-title font-bold">Safe Community</h3>
          <p class="text-caption text-ink-muted">Sistem moderasi, laporan, suspend, dan banned membantu menjaga
            komunitas tetap aman dan nyaman.</p>
        </div>
      </div>

      {{-- Content Sharing --}}
      <div class="bg-canvas rounded-lg p-4 flex flex-col gap-2 border-solid border-hairline border">
        <i class="bi bi-chat-left-text text-primary text-heading-2"></i>
        <div class="flex flex-col gap-2 items-start">
          <h3 class="text-title font-bold">Content Sharing</h3>
          <p class="text-caption text-ink-muted">Fitur-fitur yang memudahkan anggota komunitas untuk berbagi konten yang
            relevan dengan hobi mereka, seperti artikel, foto, dan video.</p>
        </div>
      </div>

      {{-- Personalization --}}
      <div class="bg-canvas rounded-lg p-4 flex flex-col gap-2 border-solid border-hairline border">
        <i class="bi bi-sliders text-primary text-heading-2"></i>
        <div class="flex flex-col gap-2 items-start">
          <h3 class="text-title font-bold">Personalization</h3>
          <p class="text-caption text-ink-muted">Fitur-fitur yang memudahkan anggota komunitas untuk menyesuaikan
            pengalaman mereka berdasarkan minat dan preferensi mereka.</p>
        </div>
      </div>

      {{-- Shared Interests --}}
      <div class="bg-canvas rounded-lg p-4 flex flex-col gap-2 border-solid border-hairline border">
        <i class="bi bi-heart text-primary text-heading-2"></i>
        <div class="flex flex-col gap-2 items-start">
          <h3 class="text-title font-bold">Shared Interests</h3>
          <p class="text-caption text-ink-muted">Fitur-fitur yang memudahkan anggota komunitas untuk menemukan dan
            berinteraksi dengan orang-orang yang memiliki minat dan hobi yang sama.</p>
        </div>
      </div>
    </div>
  </section>


  {{-- How It Works --}}
  <section class="mt-16 w-full">
    {{-- title --}}
    <div class="mb-4">
      <h2 class="text-heading-2 font-bold">How It Works</h2>
      <p class="text-caption text-ink-muted">5 langkah mudah untuk mulai bergabung</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- Create --}}
      <div class="flex flex-col mt-4 gap-2">
        <h2
          class="text-white bg-primary text-heading-2 font-bold w-[32px] h-[32px] flex items-center justify-center rounded-full">
          1</h2>
        <h2 class="text-heading-2 font-bold">Create</h2>
        <p class="text-caption text-ink-muted">Daftar dalam hitungan menit menggunakan email atau akun Google, lalu
          mulai jelajahi komunitas hobi.</p>
      </div>

      {{-- Complete Your Profile --}}
      <div class="flex flex-col mt-4 gap-2">
        <h2
          class="text-white bg-primary text-heading-2 font-bold w-[32px] h-[32px] flex items-center justify-center rounded-full">
          2</h2>
        <h2 class="text-heading-2 font-bold">Complete Your Profile</h2>
        <p class="text-caption text-ink-muted">Tambahkan foto, bio, dan pilih minatmu agar anggota lain lebih mudah
          mengenalmu.</p>
      </div>

      {{-- Choose Your Interests --}}
      <div class="flex flex-col mt-4 gap-2">
        <h2
          class="text-white bg-primary text-heading-2 font-bold w-[32px] h-[32px] flex items-center justify-center rounded-full">
          3</h2>
        <h2 class="text-heading-2 font-bold">Choose Your Interests</h2>
        <p class="text-caption text-ink-muted">Pilih hobi yang kamu sukai untuk mendapatkan rekomendasi klub yang paling
          sesuai.</p>
      </div>

      {{-- Join Your Community --}}
      <div class="flex flex-col mt-4 gap-2">
        <h2
          class="text-white bg-primary text-heading-2 font-bold w-[32px] h-[32px] flex items-center justify-center rounded-full">
          4</h2>
        <h2 class="text-heading-2 font-bold">Join Your Community</h2>
        <p class="text-caption text-ink-muted">Bergabunglah dengan komunitas hobi yang paling sesuai dengan minat kamu
          dan mulailah berinteraksi dengan anggota lain.</p>
      </div>

      {{-- Connect with Others --}}
      <div class="flex flex-col mt-4 gap-2">
        <h2
          class="text-white bg-primary text-heading-2 font-bold w-[32px] h-[32px] flex items-center justify-center rounded-full">
          5</h2>
        <h2 class="text-heading-2 font-bold">Connect with Others</h2>
        <p class="text-caption text-ink-muted">Buat hubungan baru dan berinteraksi dengan anggota komunitas hobi Anda
          melalui diskusi dan aktivitas bersama.</p>
      </div>
    </div>
  </section>


  {{-- Main Features --}}
  <section class="mt-16 w-full min-h-dvh flex flex-col justify-center">
    {{-- title --}}
    <div class="mb-4">
      <h2 class="text-heading-2 font-bold">Main Features</h2>
      <p class="text-caption text-ink-muted">Semua fitur utama Orbii, dari diskusi hingga moderasi</p>
    </div>

    <div class="flex flex-col gap-4 md:gap-6 w-full">
      {{-- Forum Discussion --}}
      <div
        class="scale-card flex flex-row gap-2 p-3 bg-canvas rounded-lg border-solid border-hairline border shrink-0 w-full">
        <div class="order-1 flex flex-col gap-1 w-full">
          <h3 class="font-bold text-body-mid">Forum Discussion</h3>
          <p class="text-caption text-ink-muted">Diskusikan ide, ajukan pertanyaan, dan berbagi pengalaman bersama
            anggota
            klub dalam forum yang terorganisir.</p>
        </div>
        <img src="{{ asset('images/illustration/discution.svg') }}" alt="Forum Discussion Illustration"
          class="order-2 h-[150px]" width="150" height="150">
      </div>

      {{-- Create & Share Posts --}}
      <div
        class="scale-card flex flex-row gap-2 p-3 bg-canvas rounded-lg border-solid border-hairline border shrink-0 w-full">
        <div class="order-2 flex flex-col gap-1 w-full">
          <h3 class="font-bold text-body-mid">Create & Share Posts</h3>
          <p class="text-caption text-ink-muted">Bagikan cerita, tips, dokumentasi kegiatan, atau informasi terbaru
            kepada
            seluruh anggota klub.</p>
        </div>
        <img src="{{ asset('images/illustration/share.svg') }}" alt="Create & Share Posts Illustration"
          class="order-1 h-[150px]" width="150" height="150">
      </div>

      {{-- Official Clubs Announcements --}}
      <div
        class="scale-card flex flex-row gap-2 p-3 bg-canvas rounded-lg border-solid border-hairline border shrink-0 w-full">
        <div class="order-1 flex flex-col gap-1 w-full">
          <h3 class="font-bold text-body-mid">Official Clubs Announcements</h3>
          <p class="text-caption text-ink-muted">Pengurus klub dapat menyampaikan pengumuman penting agar seluruh
            anggota
            selalu mendapatkan informasi terbaru.</p>
        </div>
        <img src="{{ asset('images/illustration/announcement.svg') }}" alt="Official Clubs Announcements Illustration"
          class="order-2 h-[150px]" width="150" height="150">
      </div>

      {{-- Interactive Comments --}}
      <div
        class="scale-card flex flex-row gap-2 p-3 bg-canvas rounded-lg border-solid border-hairline border shrink-0 w-full">
        <div class="order-2 flex flex-col gap-1 w-full">
          <h3 class="font-bold text-body-mid">Interactive Comments</h3>
          <p class="text-caption text-ink-muted">Berikan tanggapan, diskusikan ide, dan bangun percakapan langsung pada
            setiap postingan.</p>
        </div>
        <img src="{{ asset('images/illustration/comment.svg') }}" alt="Interactive Comments Illustration"
          class="order-1 h-[150px]" width="150" height="150">
      </div>

      {{-- Direct Message --}}
      <div
        class="scale-card flex flex-row gap-2 p-3 bg-canvas rounded-lg border-solid border-hairline border shrink-0 w-full">
        <div class="order-1 flex flex-col gap-1 w-full">
          <h3 class="font-bold text-body-mid">Direct Message</h3>
          <p class="text-caption text-ink-muted">Hubungi anggota lain secara pribadi untuk berdiskusi, bertanya, atau
            membangun relasi baru.</p>
        </div>
        <img src="{{ asset('images/illustration/messages.svg') }}" alt="Direct Message Illustration"
          class="order-2 h-[150px]" width="150" height="150">
      </div>

      {{-- Safe Community --}}
      <div
        class="scale-card flex flex-row gap-2 p-3 bg-canvas rounded-lg border-solid border-hairline border shrink-0 w-full">
        <div class="order-2 flex flex-col gap-1 w-full">
          <h3 class="font-bold text-body-mid">Safe Community</h3>
          <p class="text-caption text-ink-muted">Laporkan konten atau pengguna yang melanggar aturan agar komunitas
            tetap
            nyaman dan aman untuk semua.</p>
        </div>
        <img src="{{ asset('images/illustration/Safe.svg') }}" alt="Safe Community Illustration"
          class="order-1 h-[150px]" width="150" height="150">
      </div>
    </div>
  </section>


  {{-- Explore Hobbies --}}
  <section class="mt-16 w-full">
    {{-- Title --}}
    <div class="mb-4">
      <h2 class="text-heading-2 font-bold">Explore Hobbies</h2>
      <p class="text-caption text-ink-mmuted">Temukan komunitas berdasarkan hobi yang kamu sukai.</p>
    </div>

    {{-- Club Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-8 w-full">
      <div
        class="bg-canvas rounded-lg border border-solid border-hairline p-3 flex flex-col gap-2 items-start justify-center">
        <i class="fa-solid fa-music text-primary text-heading-2"></i>
        <h3 class="font-bold text-body-mid">Music</h3>
        <p class="text-caption text-ink-muted">769 Members</p>
      </div>

      <div
        class="bg-canvas rounded-lg border border-solid border-hairline p-3 flex flex-col gap-2 items-start justify-center">
        <i class="fa-solid fa-mountain text-primary text-heading-2"></i>
        <h3 class="font-bold text-body-mid">Hiking</h3>
        <p class="text-caption text-ink-muted">[32px]9 Members</p>
      </div>

      <div
        class="bg-canvas rounded-lg border border-solid border-hairline p-3 flex flex-col gap-2 items-start justify-center">
        <i class="fa-solid fa-fish text-primary text-heading-2"></i>
        <h3 class="font-bold text-body-mid">Fishing</h3>
        <p class="text-caption text-ink-muted">1.2K Members</p>
      </div>

      <div
        class="bg-canvas rounded-lg border border-solid border-hairline p-3 flex flex-col gap-2 items-start justify-center">
        <i class="fa-solid fa-camera text-primary text-heading-2"></i>
        <h3 class="font-bold text-body-mid">Photography</h3>
        <p class="text-caption text-ink-muted">1.5K Members</p>
      </div>

      <div
        class="bg-canvas rounded-lg border border-solid border-hairline p-3 flex flex-col gap-2 items-start justify-center">
        <i class="fa-solid fa-futbol text-primary text-heading-2"></i>
        <h3 class="font-bold text-body-mid">Football</h3>
        <p class="text-caption text-ink-muted">2.1K Members</p>
      </div>

      <div
        class="bg-canvas rounded-lg border border-solid border-hairline p-3 flex flex-col gap-2 items-start justify-center">
        <i class="fa-solid fa-book-open text-primary text-heading-2"></i>
        <h3 class="font-bold text-body-mid">Reading</h3>
        <p class="text-caption text-ink-muted">798K Members</p>
      </div>
    </div>
  </section>


  {{-- CTA --}}
  <section class="mt-16 w-full">
    <div class="bg-primary-active rounded-lg p-8 flex flex-col gap-4 items-center justify-center">
      <h2 class="text-white text-heading-2 sm:text-heading-1 font-bold">Ready to Join?</h2>
      <p class="text-white text-caption">Bergabunglah dengan komunitas hobi yang sesuai dengan minatmu dan temukan
        teman baru yang memiliki passion yang sama.</p>
      <a href="{{ route('register') }}"
        class="text-primary bg-white text-body-md rounded-full border border-solid border-hairline w-full p-2.5 text-center">Get
        Started</a>
      <a href="{{ route('login') }}"
        class="text-white text-body-md rounded-full border border-solid border-hairline w-full p-2.5 text-center">Login</a>
    </div>
  </section>
@endsection
