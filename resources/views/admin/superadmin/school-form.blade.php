@extends('layouts.admin.layout')

@section('content')
<div>
    <h1 class="admin-h1 font-plex my-3">Add School</h1>
    <p class="text-gray-500 mb-4">Create a school, first school admin login, and initial subscription.</p>
    @include('partials.message')

    <form method="POST" action="{{ url('/admin/schools') }}" class="bg-white custom-shadow border p-5 max-w-4xl">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">School Name</label>
                <input name="school_name" value="{{ old('school_name') }}" class="border px-3 py-2 w-full" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">School Email</label>
                <input type="email" name="school_email" value="{{ old('school_email') }}" class="border px-3 py-2 w-full" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Phone</label>
                <input name="phone" value="{{ old('phone') }}" class="border px-3 py-2 w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Monthly Plan</label>
                <select name="plan_id" class="border px-3 py-2 w-full" required>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->display_name ?? $plan->name }} - {{ number_format((float) $plan->amount, 2) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Admin Name</label>
                <input name="admin_name" value="{{ old('admin_name') }}" class="border px-3 py-2 w-full" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Admin Login Email</label>
                <input type="email" name="admin_email" value="{{ old('admin_email') }}" class="border px-3 py-2 w-full" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Admin Password</label>
                <input name="admin_password" placeholder="Leave blank to auto-generate" class="border px-3 py-2 w-full">
            </div>
        </div>
        <div class="mt-5">
            <button class="bg-red-700 text-white px-4 py-2 rounded">Create School</button>
            <a href="{{ url('/admin/schools') }}" class="ml-3 text-gray-600">Cancel</a>
        </div>
    </form>
</div>
@endsection
