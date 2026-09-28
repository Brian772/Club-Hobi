@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Dashboard</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Halo, {{ auth()->user()?->name ?? 'Pengguna' }}</h1>
            </div>

            <a href="{{ route('posts.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-500">
                + Buat Postingan
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Club yang diikuti</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ optional($joinedClub)->count() ?? 0 }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Postingan</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ optional($feedPosts)->count() ?? 0 }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Status</p>
                <p class="mt-3 text-lg font-semibold text-emerald-600">Akun aktif</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.2fr,1fr]">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900">Club yang anda ikuti</h2>
                    <a href="{{ route('clubs.index') }}" class="text-sm font-medium text-sky-600 hover:text-sky-500">Lihat selengkapnya →</a>
                </div>

                @if (!empty($joinedClub) && $joinedClub->isNotEmpty())
                    <div class="space-y-4">
                        @foreach ($joinedClub as $club)
                            <a href="{{ route('clubs.show', $club->id) }}" class="block rounded-xl border border-slate-200 p-3 transition hover:border-sky-200 hover:bg-sky-50">
                                <div class="flex items-start gap-3">
                                    <img src="{{ $club->cover_display_url ?? asset('images/default-club.png') }}" alt="{{ $club->name }}" class="h-16 w-16 rounded-lg object-cover">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $club->hobby->name ?? 'Kategori' }}</p>
                                        <h3 class="mt-1 text-base font-semibold text-slate-900">{{ $club->name }}</h3>
                                        <p class="mt-1 text-sm text-slate-600 line-clamp-2">{{ $club->description }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">Anda belum bergabung ke klub manapun.</p>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">Aktivitas terbaru</h2>

                @if (!empty($feedPosts) && $feedPosts->isNotEmpty())
                    <div class="mt-4 space-y-4">
                        @foreach ($feedPosts->take(4) as $post)
                            <div class="rounded-xl border border-slate-200 p-3">
                                <p class="text-sm font-semibold text-slate-900">{{ $post->user->name ?? 'Pengguna' }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ Str::limit($post->content ?? 'Tidak ada konten', 120) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">Belum ada aktivitas terbaru.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
