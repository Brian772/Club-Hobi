@extends('layouts.app')

@section('content')
  <div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-3xl font-bold text-neutral-900">Dokumentasi</h1>
        <p class="text-sm text-neutral-500 mt-1">File dan dokumentasi dari klub yang Anda ikuti.</p>
      </div>
      <a href="{{ route('konten.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg">Tambah konten</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @forelse ($files as $file)
        <a href="{{ route('konten.show', $file->id) }}" class="bg-white rounded-xl border border-neutral-200 p-5 hover:shadow-md transition-shadow">
          <p class="text-xs text-neutral-500">{{ $file->club->name ?? 'Klub' }} · {{ ucfirst($file->type) }}</p>
          <h2 class="font-semibold text-lg text-neutral-900 mt-1">{{ $file->title }}</h2>
          <p class="text-sm text-neutral-500 mt-2">Diunggah {{ optional($file->uploaded_at)->diffForHumans() }}</p>
        </a>
      @empty
        <p class="text-neutral-500">Belum ada dokumentasi.</p>
      @endforelse
    </div>
  </div>
@endsection