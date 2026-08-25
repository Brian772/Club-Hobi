@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>
    <p>Welcome to the admin dashboard!</p>
  </div>
@endsection

