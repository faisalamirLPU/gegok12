@extends('layouts.admin.layout')

@section('title', 'Collection Summary Report')

@section('content')
<div class="relative">

    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Finance Reports</h1>
            <p class="text-sm text-gray-500">Enterprise Financial Analytics and Reporting</p>
        </div>
    </div>

    @include('admin.finance.reports.partials.tabs')

    {{-- Filter Form --}}
    <div class="bg-white p-4 rounded border mb-6 shadow-sm">
        <form action="{{ route('finance.reports.collection') }}" method="GET" class="flex items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" value="{{ $date }}" class="w-full text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-blue-700">Filter</button>
            </div>
        </form>
    </div>

    {{-- Daily Collection Summary --}}
    <h2 class="text-lg font-bold text-gray-700 mb-3">Daily Collection ({{ \Carbon\Carbon::parse($date)->format('d M Y') }})</h2>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Collection</p>
            <p class="text-2xl font-black text-gray-800 mt-1">Rs. {{ number_format($dailyCollection['total'], 0) }}</p>
        </div>
        
        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Cash</p>
            <p class="text-xl font-bold text-gray-700 mt-1">Rs. {{ number_format($dailyCollection['cash'], 0) }}</p>
        </div>

        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-purple-500">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Online</p>
            <p class="text-xl font-bold text-gray-700 mt-1">Rs. {{ number_format($dailyCollection['online'], 0) }}</p>
        </div>

        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-yellow-500">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Cheque</p>
            <p class="text-xl font-bold text-gray-700 mt-1">Rs. {{ number_format($dailyCollection['cheque'], 0) }}</p>
        </div>

        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-indigo-500">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Advance Recv.</p>
            <p class="text-xl font-bold text-gray-700 mt-1">Rs. {{ number_format($dailyCollection['advance_received'], 0) }}</p>
        </div>
    </div>

    {{-- Monthly Collection Trends --}}
    <h2 class="text-lg font-bold text-gray-700 mb-3">Monthly Trends (Current Academic Year)</h2>
    <div class="bg-white rounded border shadow-sm overflow-hidden mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month / Year</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Collection</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($monthlyCollection as $month)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ \Carbon\Carbon::createFromDate($month->year, $month->month, 1)->format('F Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800 text-right">
                        Rs. {{ number_format($month->total, 0) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No data found for this academic year.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
