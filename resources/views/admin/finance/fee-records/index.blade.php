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
    <div class="bg-white custom-shadow border p-4 mb-4 mt-4">
        <form method="GET" action="{{ route('finance.fee-records.index') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Select Month</label>
                <select name="month" class="rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-0">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected(request('month', now()->month) == $m)>
                            {{ Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Year</label>
                <select name="year" class="rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-0">
                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected(request('year', now()->year) == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="no-underline text-white px-6 flex items-center custom-green py-1.5 justify-center">
                    <span class="text-sm font-semibold">View Classes</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Classes Grid --}}
    <div class="flex flex-wrap -mx-1">
        @forelse($classes as $link)
            @php
                $studentCount = $link->student_count ?? 0;
            @endphp
            <div class="w-full lg:w-1/3 md:w-1/2 px-1 my-2">
                <a href="{{ route('finance.fee-records.by-class', $link->id) }}?month={{ request('month', now()->month) }}&year={{ request('year', now()->year) }}"
                   class="bg-white custom-shadow border p-5 hover:border-blue-300 transition-all block group">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition">
                                {{ $link->standard->name ?? 'N/A' }}
                                {{ $link->section->name ?? '' }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $studentCount }} Students</p>
                        </div>
                        <div class="text-gray-400 group-hover:text-blue-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="w-full px-1">
                <div class="bg-white custom-shadow border p-12 text-center my-2 text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <p class="text-sm">No classes found with students enrolled.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection