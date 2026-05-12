<!-- Finance Navigation Menu -->
<div class="space-y-1">
    <a href="{{ route('finance.fee-management.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('finance.fee-management.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
        <span class="text-xl">📊</span>
        <span>Fee Management</span>
    </a>

    <!-- Submenu for Fee Management -->
    @if (request()->routeIs('finance.fee-management.*'))
        <div class="ml-4 space-y-1 border-l-2 border-blue-300 pl-2">
            <a href="{{ route('finance.fee-management.index') }}" class="flex items-center gap-2 px-4 py-2 rounded {{ request()->routeIs('finance.fee-management.index') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
                <span>🎯</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('finance.fee-management.categories') }}" class="flex items-center gap-2 px-4 py-2 rounded {{ request()->routeIs('finance.fee-management.categories') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
                <span>📋</span>
                <span>Categories</span>
            </a>
            <a href="{{ route('finance.fee-management.structures') }}" class="flex items-center gap-2 px-4 py-2 rounded {{ request()->routeIs('finance.fee-management.structures') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
                <span>📊</span>
                <span>Structures</span>
            </a>
            <a href="{{ route('finance.fee-management.special-fees') }}" class="flex items-center gap-2 px-4 py-2 rounded {{ request()->routeIs('finance.fee-management.special-fees') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
                <span>⚡</span>
                <span>Special Fees</span>
            </a>
            <a href="{{ route('finance.fee-management.payments') }}" class="flex items-center gap-2 px-4 py-2 rounded {{ request()->routeIs('finance.fee-management.payments') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
                <span>💳</span>
                <span>Payments</span>
            </a>
            <a href="{{ route('finance.fee-management.analytics') }}" class="flex items-center gap-2 px-4 py-2 rounded {{ request()->routeIs('finance.fee-management.analytics') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
                <span>📈</span>
                <span>Analytics</span>
            </a>
        </div>
    @endif

    <!-- Legacy Routes (optional, can be hidden after transition) -->
    <div class="mt-4 border-t pt-4">
        <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Legacy Finance</p>
        <a href="{{ route('finance.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
            <span class="text-xl">📈</span>
            <span>Dashboard</span>
        </a>
    </div>
</div>
