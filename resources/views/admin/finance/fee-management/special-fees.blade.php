@extends('layouts.admin.layout')

@section('title', 'Special Fees')

@section('content')
<div class="relative">
    {{-- Page Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Special Fees</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.special-fees.create') }}"
               class="no-underline text-white px-4 my-3 mx-1 flex items-center custom-green py-1 justify-center">
                <span class="mx-1 text-sm font-semibold">Assign Fee</span>
                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 409.6 409.6" xml:space="preserve" class="w-3 h-3 fill-current text-white"><g><g><path d="M392.533,187.733H221.867V17.067C221.867,7.641,214.226,0,204.8,0s-17.067,7.641-17.067,17.067v170.667H17.067
                  C7.641,187.733,0,195.374,0,204.8s7.641,17.067,17.067,17.067h170.667v170.667c0,9.426,7.641,17.067,17.067,17.067
                  s17.067-7.641,17.067-17.067V221.867h170.667c9.426,0,17.067-7.641,17.067-17.067S401.959,187.733,392.533,187.733z"></path></g></g></svg>
            </a>
        </div>
    </div>

    {{-- Navigation --}}
    @include('admin.finance.partials.tabs')

    {{-- Special Fees Table --}}
    <div class="mt-5 bg-white custom-shadow border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide hidden md:table-cell">Category</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide hidden sm:table-cell">Due Date</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($specialFees as $fee)
                        <tr class="hover:bg-gray-50">
                            {{-- Student --}}
                            <td class="px-4 py-3">
                                @php
                                    $profile = optional($fee->student)->userprofile;
                                    $studentName = trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? '')) ?: ($fee->student->name ?? 'N/A');
                                @endphp
                                <div class="font-medium text-gray-900">{{ $studentName }}</div>
                                <div class="text-xs text-gray-400">
                                    @if(optional($fee->student)->roll_number)
                                        ID: {{ $fee->student->roll_number }}
                                    @else
                                        ID: {{ $fee->user_id }}
                                    @endif
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $fee->feeCategory->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Amount --}}
                            <td class="px-4 py-3 text-right">
                                <span class="font-semibold text-gray-900">Rs. {{ number_format($fee->amount, 0) }}</span>
                            </td>

                            {{-- Due Date --}}
                            <td class="px-4 py-3 hidden sm:table-cell">
                                @if($fee->due_date)
                                    <div class="text-xs @if($fee->due_date->lt(now()) && $fee->status == 1) text-red-600 font-semibold @else text-gray-600 @endif">
                                        {{ $fee->due_date->format('d M Y') }}
                                    </div>
                                    @if($fee->due_date->lt(now()) && $fee->status == 1)
                                        <span class="text-xs text-red-500">Overdue</span>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusMap = [
                                        1 => ['bg-green-100', 'text-green-700', 'Active'],
                                        2 => ['bg-blue-100', 'text-blue-700', 'Applied'],
                                        0 => ['bg-gray-100', 'text-gray-600', 'Inactive'],
                                    ];
                                    $s = $statusMap[$fee->status] ?? ['bg-gray-100', 'text-gray-600', 'Unknown'];
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $s[0] }} {{ $s[1] }}">
                                    {{ $s[2] }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('finance.special-fees.edit', $fee->id) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-md transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('finance.special-fees.destroy', $fee->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this special fee?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-md transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <p class="font-medium">No special fees assigned</p>
                                <p class="text-sm text-gray-400 mt-1"><a href="{{ route('finance.special-fees.create') }}" class="text-blue-600 hover:underline">Assign a special fee</a></p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($specialFees->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $specialFees->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection