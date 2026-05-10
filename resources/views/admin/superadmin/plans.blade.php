@extends('layouts.admin.layout')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="admin-h1 font-plex my-3">Plans</h1>
            <p class="text-gray-500 mb-4">Subscription plans available for schools.</p>
        </div>
        <a href="{{ url('/admin/plans/create') }}" class="bg-red-700 text-white px-4 py-2 rounded">Add Plan</a>
    </div>
    @include('partials.message')

    <div class="bg-white custom-shadow border overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-3 px-4">Plan</th>
                    <th class="text-left py-3 px-4">Cycle</th>
                    <th class="text-left py-3 px-4">Amount</th>
                    <th class="text-left py-3 px-4">Members</th>
                    <th class="text-left py-3 px-4">Subscriptions</th>
                    <th class="text-left py-3 px-4">Status</th>
                    <th class="text-left py-3 px-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $plan->display_name ?? $plan->name }}</td>
                        <td class="py-3 px-4">{{ $plan->cycle }} days</td>
                        <td class="py-3 px-4">{{ number_format((float) $plan->amount, 2) }}</td>
                        <td class="py-3 px-4">{{ $plan->no_of_members ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $plan->subscription_count }}</td>
                        <td class="py-3 px-4">
                            <span class="px-3 py-1 rounded-full text-xs {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="py-3 px-4"><a class="text-blue-600" href="{{ url('/admin/plans/'.$plan->id.'/edit') }}">Edit</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 px-4 text-center text-gray-500">No plans found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $plans->links() }}</div>
</div>
@endsection
