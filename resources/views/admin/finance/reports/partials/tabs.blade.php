<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <a href="{{ route('finance.reports.collection') }}"
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('finance.reports.collection') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Collection Summary
        </a>

        <a href="{{ route('finance.reports.outstanding') }}"
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('finance.reports.outstanding') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Outstanding Dues
        </a>

        <a href="{{ route('finance.reports.advance-balances') }}"
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('finance.reports.advance-balances') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Advance Balances
        </a>

        <a href="{{ route('finance.reports.aging') }}"
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('finance.reports.aging') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Fee Aging Report
        </a>
    </nav>
</div>
