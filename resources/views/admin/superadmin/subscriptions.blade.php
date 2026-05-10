@extends('layouts.admin.layout')

@section('content')
<div>
    <h1 class="admin-h1 font-plex my-3">Subscriptions</h1>
    <p class="text-gray-500 mb-4">Current and past school subscriptions.</p>

    <div class="bg-white custom-shadow border overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-3 px-4">School</th>
                    <th class="text-left py-3 px-4">Plan</th>
                    <th class="text-left py-3 px-4">Owner</th>
                    <th class="text-left py-3 px-4">Ends</th>
                    <th class="text-left py-3 px-4">Status</th>
                    <th class="text-left py-3 px-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $subscription)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ optional($subscription->school)->name ?? '-' }}</td>
                        <td class="py-3 px-4">{{ optional($subscription->plan)->display_name ?? optional($subscription->plan)->name ?? '-' }}</td>
                        <td class="py-3 px-4">{{ optional($subscription->user)->name ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $subscription->end_date ? date('d-m-Y', strtotime($subscription->end_date)) : '-' }}</td>
                        <td class="py-3 px-4">{{ ucwords($subscription->status ?? '-') }}</td>
                        <td class="py-3 px-4">
                            @if($subscription->status !== 'approve')
                                <form method="POST" action="{{ url('/admin/subscriptions/'.$subscription->id.'/approve') }}">
                                    @csrf
                                    <button class="text-blue-600">Approve</button>
                                </form>
                            @else
                                Approved
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 px-4 text-center text-gray-500">No subscriptions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $subscriptions->links() }}</div>
</div>
@endsection
