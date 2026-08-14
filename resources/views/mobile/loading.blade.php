@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="mobile-screen loading-screen">
    <div>
        <img src="{{ asset('image/rafiki.png') }}" alt="Loading" style="width: 120px; margin: 0 auto 18px; display: block;" />
        <h1>Loading</h1>
        <p>Silakan tunggu sebentar, kami sedang menyiapkan konten untukmu.</p>
    </div>

    <div class="loading-dots">
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>
@endsection