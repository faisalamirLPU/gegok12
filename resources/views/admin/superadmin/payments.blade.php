@extends('layouts.admin.layout')

@section('content')
<div>
    <h1 class="admin-h1 font-plex my-3">Payments</h1>
    <p class="text-gray-500 mb-4">Subscription records with payment details.</p>

    <div class="bg-white custom-shadow border overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-3 px-4">School</th>
                    <th class="text-left py-3 px-4">Plan</th>
                    <th class="text-left py-3 px-4">Amount</th>
                    <th class="text-left py-3 px-4">Transaction</th>
                    <th class="text-left py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    @php($details = $payment->payment_details ?? [])
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ optional($payment->school)->name ?? '-' }}</td>
                        <td class="py-3 px-4">{{ optional($payment->plan)->display_name ?? optional($payment->plan)->name ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $details['amount'] ?? number_format((float) optional($payment->plan)->amount, 2) }}</td>
                        <td class="py-3 px-4">{{ $details['txnid'] ?? '-' }}</td>
                        <td class="py-3 px-4">{{ ucwords($details['status'] ?? $payment->status ?? '-') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 px-4 text-center text-gray-500">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $payments->links() }}</div>
</div>
@endsection
