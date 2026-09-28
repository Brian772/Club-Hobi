@extends('layouts.app')

@section('content')
  <header>
    <h2 class="text-2xl text-ink font-bold">Club Requests</h2>
  </header>

  <main x-data="{ tab: 'pending' }" class="mt-6 border borde-hairline p-4 rounded-lg">
    <?php
    use App\Models\ClubRequest;
    $clubRequest = ClubRequest::where('status', 'pending')->get();
    $clubRequestRejected = ClubRequest::where('status', 'rejected')->get();
    $clubRequestApproved = ClubRequest::where('status', 'approved')->get();
    ?>

    <div class="flex flex-row gap-2 border-b border-hairiline overflow-auto">
      <button @click="tab = 'pending'"
        :class="tab === 'pending' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted'"
        class="px-4 py-2 text-body font-semibold focus:outline-none">
        Pending Request
      </button>
      <button @click="tab = 'approved'"
        :class="tab === 'approved' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted'"
        class="px-4 py-2 text-body font-semibold focus:outline-none">
        Approved Request
      </button>
      <button @click="tab = 'rejected'"
        :class="tab === 'rejected' ? 'border-b-2 border-primary text-primary' : 'text-ink-muted'"
        class="px-4 py-2 text-body font-semibold focus:outline-none">
        Rejected Request
      </button>
    </div>

    <div x-show="tab === 'pending'" class="flex flex-col mt-4 gap-4">
      @if ($clubRequest->isEmpty())
        <p class="text-body-mid text-ink-muted">No pending requests found.</p>
      @endif
      @foreach ($clubRequest as $request)
        <x-club-request-card :request="$request" />
      @endforeach
    </div>
    <div x-show="tab === 'approved'" class="flex flex-col mt-4 gap-4">  
      @if ($clubRequestApproved->isEmpty())
        <p class="text-body-mid text-ink-muted">No approved club requests found.</p>
      @endif
      @foreach ($clubRequestApproved as $request)
        <x-club-request-card :request="$request" />
      @endforeach
    </div>
    <div x-show="tab === 'rejected'" class="flex flex-col mt-4 gap-4">
      @if ($clubRequestRejected->isEmpty())
        <p class="text-body-mid text-ink-muted">No rejected club requests found.</p>
      @endif
      @foreach ($clubRequestRejected as $request)
        <x-club-request-card :request="$request" />
      @endforeach
    </div>
  </main>
@endsection
