<div class="flex flex-wrap items-center gap-2 mb-6 border-b border-gray-200">
    <a href="{{ route('finance.fee-management.index') }}" 
       class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ request()->routeIs('finance.fee-management.index') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        Dashboard
    </a>
    <a href="{{ route('finance.fee-management.categories') }}" 
       class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ request()->routeIs('finance.fee-management.categories') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        Categories
    </a>
    <a href="{{ route('finance.fee-management.structures') }}" 
       class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ request()->routeIs('finance.fee-management.structures') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        Structures
    </a>
    <a href="{{ route('finance.fee-management.special-fees') }}" 
       class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ request()->routeIs('finance.fee-management.special-fees') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        Special Fees
    </a>
    <a href="{{ route('finance.fee-management.payments') }}" 
       class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ request()->routeIs('finance.fee-management.payments') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        Payments
    </a>
    <a href="{{ route('finance.fee-management.analytics') }}" 
       class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ request()->routeIs('finance.fee-management.analytics') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        Analytics
    </a>
</div>
