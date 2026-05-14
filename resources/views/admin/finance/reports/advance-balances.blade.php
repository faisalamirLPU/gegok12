@extends('layouts.admin.layout')

@section('title', 'Advance Balances Report')

@section('content')
<div class="relative">

    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Finance Reports</h1>
            <p class="text-sm text-gray-500">Enterprise Financial Analytics and Reporting</p>
        </div>
    </div>

    @include('admin.finance.reports.partials.tabs')

    {{-- Advance List --}}
    <div class="bg-white rounded border shadow-sm overflow-hidden mb-8">
        <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Student Advance Credits</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Received</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Applied</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Available Advance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($records as $record)
                    @php $user = $record['user']; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ optional($user->userprofile)->firstname }} {{ optional($user->userprofile)->lastname }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-medium">
                            <a href="{{ route('finance.fee-records.show', $user->id ?? 0) }}" target="_blank">
                                #{{ $user->id ?? 'Unknown' }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ optional(optional($user)->studentAcademic)->standardLink->standard->name ?? '-' }} - {{ optional(optional($user)->studentAcademic)->standardLink->section->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                            Rs. {{ number_format($record['total_received'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                            Rs. {{ number_format($record['total_applied'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 text-right font-black">
                            Rs. {{ number_format($record['balance'], 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center">No students currently hold an advance balance.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
