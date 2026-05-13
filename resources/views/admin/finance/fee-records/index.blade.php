@extends('layouts.admin.layout')

@section('title', 'Student Fee Records')

@section('content')
<div class="relative">
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Student Fee Records</h1>
        </div>
    </div>

    @include('admin.finance.partials.tabs')

    {{-- Month/Year Filter --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('finance.fee-records.index') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Month</label>
                <select name="month" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected(request('month', now()->month) == $m)>
                            {{ Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select name="year" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected(request('year', now()->year) == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                    View Classes
                </button>
            </div>
        </form>
    </div>

    {{-- Classes Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($classes as $link)
            @php
                $studentCount = $link->student_count ?? 0;
            @endphp
            <a href="{{ route('finance.fee-records.by-class', $link->id) }}?month={{ request('month', now()->month) }}&year={{ request('year', now()->year) }}"
               class="bg-white rounded-lg border border-gray-200 p-5 hover:border-blue-400 hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">
                            {{ $link->standard->name ?? 'N/A' }}
                            {{ $link->section->name ?? '' }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $studentCount }} Students</p>
                    </div>
                    <div class="text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <p>No classes found with students enrolled.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection