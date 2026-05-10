@extends('layouts.admin.layout')

@section('content')
<div>
    <h1 class="admin-h1 font-plex my-3">Renew Subscription</h1>
    <p class="text-gray-500 mb-4">Scan the QR code, pay the selected plan amount, and submit the transaction reference. Renewed plans will remain inactive until approved by the super admin.</p>
    @include('partials.message')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white custom-shadow border p-5">
            <h2 class="text-xl font-semibold mb-3">Current Subscription</h2>
            <p><strong>School:</strong> {{ $school->name }}</p>
            <p><strong>Status:</strong> {{ ucwords(optional($subscription)->status ?? 'No subscription') }}</p>
            <p><strong>End Date:</strong> {{ optional($subscription)->end_date ? date('d-m-Y', strtotime($subscription->end_date)) : '-' }}</p>
        </div>

        <div class="bg-white custom-shadow border p-5">
            <h2 class="text-xl font-semibold mb-3">QR Payment</h2>
            <img class="border mb-3" alt="Payment QR" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode('GegoK12 subscription payment for '.$school->name) }}">
            <form method="POST" action="{{ url('/admin/subscription/renew') }}">
                @csrf
                <select name="plan_id" class="border px-3 py-2 w-full mb-3" required>
                    <option value="">Select monthly plan</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->display_name ?? $plan->name }} - {{ number_format((float) $plan->amount, 2) }}</option>
                    @endforeach
                </select>
                <input name="transaction_reference" class="border px-3 py-2 w-full mb-3" placeholder="Transaction reference after QR payment">
                <button class="bg-red-700 text-white px-4 py-2 rounded">Submit Renewal Request</button>
            </form>
        </div>
    </div>
</div>
@endsection
