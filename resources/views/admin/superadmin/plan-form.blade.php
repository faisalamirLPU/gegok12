@extends('layouts.admin.layout')

@section('content')
<div>
    <h1 class="admin-h1 font-plex my-3">{{ $plan->exists ? 'Edit Plan' : 'Create Plan' }}</h1>
    @include('partials.message')

    <form method="POST" action="{{ $plan->exists ? url('/admin/plans/'.$plan->id) : url('/admin/plans') }}" class="bg-white custom-shadow border p-5 max-w-3xl">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Internal Name</label>
                <input name="name" value="{{ old('name', $plan->name) }}" class="border px-3 py-2 w-full" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Display Name</label>
                <input name="display_name" value="{{ old('display_name', $plan->display_name) }}" class="border px-3 py-2 w-full" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Monthly Amount</label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount', $plan->amount) }}" class="border px-3 py-2 w-full" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Member Limit</label>
                <input type="number" name="no_of_members" value="{{ old('no_of_members', $plan->no_of_members ?: 500) }}" class="border px-3 py-2 w-full" required>
            </div>
        </div>
        <label class="inline-flex items-center mt-4">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->exists ? $plan->is_active : true) ? 'checked' : '' }}>
            <span class="ml-2">Active monthly plan</span>
        </label>
        <div class="mt-5">
            <button class="bg-red-700 text-white px-4 py-2 rounded">Save Plan</button>
            <a href="{{ url('/admin/plans') }}" class="ml-3 text-gray-600">Cancel</a>
        </div>
    </form>
</div>
@endsection
