@extends('layouts.app')

@section('title', 'Orbii | Appeal Account')

@section('content')
  @if ($user->status === 'active')
    <div
      class="w-caption md:w-md lg:w-2xl flex flex-col justify-start items-center border border-hairline rounded-lg bg-canvas p-6 lg:p-8">
      <header class="w-full flex flex-row justify-start items-center mb-2">
        <a href="{{ route('dashboard') }}"
          class="flex items-center text-caption text-ink-muted hover:text-ink transition-colors duration-200">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-4 h-4 mr-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
          </svg>
          Back
        </a>
      </header>
      <h1 class="text-lg font-semibold text-ink">Your account is active</h1>
      <p class="text-caption text-ink-muted mt-1">You do not need to submit an appeal at this time.</p>
    </div>
  @elseif ($user->status === 'suspended' || $user->status === 'banned')
    <div
      class="w-caption md:w-md lg:w-2xl flex flex-col justify-start items-center border border-hairline rounded-lg bg-canvas p-6 lg:p-8">
      <header class="w-full flex flex-row justify-between items-center mb-2">
        <a href="{{ $user->status === 'banned' ? route('banned') : route('dashboard') }}"
          class="flex items-center text-caption text-ink-muted hover:text-ink transition-colors duration-200">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-4 h-4 mr-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
          </svg>
          Back
        </a>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit"
            class="text-caption text-accent-red cursor-pointer text-decoration-none hover:underline hover:underline-offset-2">Logout</button>
        </form>
      </header>
      <h1 class="text-lg font-semibold text-ink">Appeal Your Account</h1>
      <p class="text-caption text-ink-muted mt-1">Request a review of the action taken against your account.</p>

      @if ($rejectedAppeal)
        <div class="w-full p-2 rounded-md border border-hairline mt-4">
          <h2 class="text-body-mid text-ink font-semibold">Previous Appeal</h2>
          <p class="text-caption text-ink-muted mt-1">Your previous appeal was rejected. You may submit a new appeal if you
            believe there are new grounds for review.</p>
          <div class="mt-2 flexflex-col gap-1">
            <p class="text-caption text-ink-muted">Rejected Reason:</p>
            <p class="text-caption text-ink">{{ $rejectedAppeal->admin_note ?? 'No reason provided.' }}</p>
          </div>
        </div>
      @endif

      <div class="w-full p-2 rounded-md border border-hairline mt-4">
        <h2 class="text-body-mid text-ink font-semibold">Account Info</h2>

        <div class="mt-2">
          <p class="text-caption text-ink-muted">Name: <span class="text-ink">{{ $user->name }}</span></p>
          <p class="text-caption text-ink-muted">Email: <span class="text-ink">{{ $user->email }}</span></p>
          <p class="text-caption text-ink-muted">Status:
            @if ($user->status === 'suspended')
              <span class="text-accent-yellow font-semibold">{{ ucfirst($user->status) }}</span>
            @elseif ($user->status === 'banned')
              <span class="text-accent-red font-semibold">{{ ucfirst($user->status) }}</span>
            @endif
          </p>
          <div class="mt-2 flexflex-col gap-1">
            <p class="text-caption text-ink-muted">Reason
              @if ($user->status === 'suspended')
                for Suspension:
              @elseif ($user->status === 'banned')
                for Ban:
              @endif
            </p>
            <p class="text-caption text-ink">{{ $user->reason }}</p>
          </div>
        </div>
      </div>
      @if ($alreadyAppealed)
        <div class="w-full p-2 rounded-md border border-hairline mt-4">
          <h2 class="text-body-mid text-ink font-semibold">Your Appeal</h2>
          <p class="text-caption text-ink-muted mt-1">You have already submitted an appeal. Please wait for it to be
            reviewed.</p>
          <div class="mt-2 flexflex-col gap-1">
            <p class="text-caption text-ink-muted">Reason for Appeal:</p>
            <p class="text-caption text-ink">{{ $appeal->reason }}</p>
            <p class="text-caption text-ink-muted mt-1">Status:
              @if ($appeal->status === 'pending')
                <span class="text-accent-yellow font-semibold">{{ ucfirst($appeal->status) }}</span>
              @elseif ($appeal->status === 'approved')
                <span class="text-accent-green font-semibold">{{ ucfirst($appeal->status) }}</span>
              @elseif ($appeal->status === 'rejected')
                <span class="text-accent-red font-semibold">{{ ucfirst($appeal->status) }}</span>
              @endif
            </p>
          </div>
        </div>
      @endif

      @if (!$alreadyAppealed)
        <form action="{{ route('appeal.store') }}" method="POST" class="w-full mt-4">
          @csrf
          <div class="mb-4">
            <label for="reason" class="block text-caption font-medium text-ink">Reason for Your Appeal</label>
            <span class="text-caption text-ink-muted">Explain why you believe this decision should be reviewed.</span>
            <textarea id="reason" name="reason" rows="4" placeholder="Write your appeal..." required
              class="mt-1 block w-full rounded-md border border-hairline bg-canvas px-3 py-2 text-caption text-ink focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
          </div>

          <button type="submit"
            class="w-full rounded-md bg-blue-600 px-4 py-2 text-caption font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            Submit Appeal
          </button>
        </form>
      @endif

    </div>
  @endif
@endsection
