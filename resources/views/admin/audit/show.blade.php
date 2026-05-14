@extends('layouts.admin.layout')

@section('title', 'Audit Trail Details')

@section('content')

<div class="relative bg-white shadow-md sm:rounded-lg mb-6">
    <div class="p-4 flex flex-wrap justify-between items-center bg-gray-50 border-b">
        <div class="flex items-center gap-3">
            <a href="{{ route('audit-trails.index') }}" class="text-gray-500 hover:text-blue-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">Audit Log Details</h2>
        </div>
        <div>
            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                {{ strtoupper($trail->action_type) }}
            </span>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 p-4 rounded border">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Event Information</p>
                <div class="space-y-2 text-sm text-gray-800">
                    <p><span class="font-medium text-gray-500 w-24 inline-block">Timestamp:</span> {{ $trail->created_at->format('d M Y, h:i:s A') }}</p>
                    <p><span class="font-medium text-gray-500 w-24 inline-block">Actor:</span> {{ optional($trail->user->userprofile)->firstname ?? 'System' }} {{ optional($trail->user->userprofile)->lastname ?? '' }}</p>
                    <p><span class="font-medium text-gray-500 w-24 inline-block">IP Address:</span> {{ $trail->ip_address }}</p>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded border">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Entity Information</p>
                <div class="space-y-2 text-sm text-gray-800">
                    <p><span class="font-medium text-gray-500 w-24 inline-block">Model:</span> {{ class_basename($trail->model_type) }}</p>
                    <p><span class="font-medium text-gray-500 w-24 inline-block">Record ID:</span> #{{ $trail->model_id }}</p>
                    <p><span class="font-medium text-gray-500 w-24 inline-block">Description:</span> {{ $trail->description }}</p>
                </div>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Payload Differences</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            {{-- Old Values --}}
            <div class="border rounded border-red-200 overflow-hidden">
                <div class="bg-red-50 px-4 py-2 border-b border-red-200 flex justify-between">
                    <span class="font-bold text-red-700 text-sm">Previous State</span>
                </div>
                <div class="p-0 bg-gray-900">
                    <pre class="text-red-400 text-xs p-4 overflow-x-auto whitespace-pre-wrap font-mono">@if($trail->old_values){!! json_encode($trail->old_values, JSON_PRETTY_PRINT) !!}@else<span class="text-gray-500 italic">No previous state recorded (creation event or untracked state)</span>@endif</pre>
                </div>
            </div>

            {{-- New Values --}}
            <div class="border rounded border-green-200 overflow-hidden">
                <div class="bg-green-50 px-4 py-2 border-b border-green-200 flex justify-between">
                    <span class="font-bold text-green-700 text-sm">New State</span>
                </div>
                <div class="p-0 bg-gray-900">
                    <pre class="text-green-400 text-xs p-4 overflow-x-auto whitespace-pre-wrap font-mono">@if($trail->new_values){!! json_encode($trail->new_values, JSON_PRETTY_PRINT) !!}@else<span class="text-gray-500 italic">No new state recorded (deletion event or untracked state)</span>@endif</pre>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection
