@extends('layouts.app')

@section('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
    <header class="w-full border-b border-hairline pb-4 mb-4">
        <h1 class="text-heading-1 text-bold">Edit Club {{ $club->name }}</h1>
    </header>
@endsection