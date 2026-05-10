@extends('layouts.admin.layout')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="admin-h1 font-plex my-3">Schools</h1>
            <p class="text-gray-500 mb-4">All schools registered on the platform.</p>
        </div>
        <a href="{{ url('/admin/schools/create') }}" class="bg-red-700 text-white px-4 py-2 rounded">Add School</a>
    </div>
    @include('partials.message')

    <div class="bg-white custom-shadow border overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-3 px-4">School</th>
                    <th class="text-left py-3 px-4">Email</th>
                    <th class="text-left py-3 px-4">Phone</th>
                    <th class="text-left py-3 px-4">Users</th>
                    <th class="text-left py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schools as $school)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $school->name }}</td>
                        <td class="py-3 px-4">{{ $school->email ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $school->phone ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $school->user_count }}</td>
                        <td class="py-3 px-4">
                            <span class="px-3 py-1 rounded-full text-xs {{ $school->status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $school->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 px-4 text-center text-gray-500">No schools found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $schools->links() }}</div>
</div>
@endsection
