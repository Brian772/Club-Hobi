@extends('layouts.app')

@section('content')
  <header>
    <h2 class="text-heading-2 text-ink">Club Requests</h2>
  </header>

  <main class="mt-6">
    <?php
    use App\Models\ClubRequest;
    $clubRequest = ClubRequest::where('status', 'pending')->get();
    $clubRequestRejected = ClubRequest::where('status', 'rejected')->get();
    $clubRequestApproved = ClubRequest::where('status', 'approved')->get();
    ?>

    <div class="flex flex-col border-b border-ink-faint pb-4 mb-4">
      <h3 class="text-heading-3 text-ink pb-2">Pending Request</h3>
      @if ($clubRequest->isEmpty())
        <p class="text-body-mid text-ink-muted">No club requests found.</p>
      @endif
      @foreach ($clubRequest as $request)
        <x-club-request-card :request="$request" />
      @endforeach
    </div>
    <div class="flex flex-col border-b border-ink-faint pb-4 mb-4">
      <h3 class="text-heading-3 text-ink pb-2">Approved Request</h3>
      @foreach ($clubRequestApproved as $request)
        <x-club-request-card :request="$request" />
      @endforeach
    </div>
    <div class="flex flex-col pb-4 mb-4">
      <h3 class="text-heading-3 text-ink pb-2">Rejected Request</h3>
      @foreach ($clubRequestRejected as $request)
        <x-club-request-card :request="$request" />
      @endforeach
    </div>
  </main>
@endsection
