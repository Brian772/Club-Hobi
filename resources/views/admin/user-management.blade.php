@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">User Management</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
      @foreach ($users as $user)
        <div class="bg-white p-4 rounded-md shadow">
          <h2 class="text-lg font-semibold">{{ $user->name }}</h2>
          <p class="text-gray-600">{{ $user->email }}</p>
          <p class="text-sm text-gray-500">Role: {{ $user->role_global }}</p>
        </div>
      @endforeach
    </div>
    {{ $users->links() }}
  </div>
@endsection
