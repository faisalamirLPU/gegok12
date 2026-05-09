<div class="w-full min-h-screen bg-red-800 text-white">

    {{-- Logo/Header --}}
    <div class="px-4 py-5 border-b border-red-700">

        <h2 class="text-lg font-bold">
            SaaS Panel
        </h2>

        <p class="text-xs text-red-200 mt-1">
            Super Administrator
        </p>

    </div>


    {{-- Navigation --}}
    <ul class="list-reset text-sm mt-2">

        {{-- Dashboard --}}
        <li class="{{ Request::segment(2) == 'dashboard' ? 'bg-red-900' : '' }}">

            <a href="{{ url('superadmin/dashboard') }}"
               class="flex items-center px-5 py-4 text-white no-underline hover:bg-red-700 transition duration-200">

                <span class="text-base">
                    📊
                </span>

                <span class="ml-3">
                    Dashboard
                </span>

            </a>

        </li>


        {{-- Schools --}}
        <li>

            <a href="#"
               class="flex items-center px-5 py-4 text-white no-underline hover:bg-red-700 transition duration-200">

                <span class="text-base">
                    🏫
                </span>

                <span class="ml-3">
                    Schools
                </span>

            </a>

        </li>


        {{-- Plans --}}
        <li class="{{ Request::segment(2) == 'plans' ? 'bg-red-900' : '' }}">

            <a href="#"
               class="flex items-center px-5 py-4 text-white no-underline hover:bg-red-700 transition duration-200">

                <span class="text-base">
                    💳
                </span>

                <span class="ml-3">
                    Plans
                </span>

            </a>

        </li>


        {{-- Subscriptions --}}
        <li>

            <a href="#"
               class="flex items-center px-5 py-4 text-white no-underline hover:bg-red-700 transition duration-200">

                <span class="text-base">
                    📦
                </span>

                <span class="ml-3">
                    Subscriptions
                </span>

            </a>

        </li>


        {{-- Users --}}
        <li>

            <a href="#"
               class="flex items-center px-5 py-4 text-white no-underline hover:bg-red-700 transition duration-200">

                <span class="text-base">
                    👥
                </span>

                <span class="ml-3">
                    Users
                </span>

            </a>

        </li>


        {{-- Analytics --}}
        <li>

            <a href="#"
               class="flex items-center px-5 py-4 text-white no-underline hover:bg-red-700 transition duration-200">

                <span class="text-base">
                    📈
                </span>

                <span class="ml-3">
                    Analytics
                </span>

            </a>

        </li>


        {{-- Payments --}}
        <li>

            <a href="#"
               class="flex items-center px-5 py-4 text-white no-underline hover:bg-red-700 transition duration-200">

                <span class="text-base">
                    💰
                </span>

                <span class="ml-3">
                    Payments
                </span>

            </a>

        </li>


        {{-- Settings --}}
        <li>

            <a href="#"
               class="flex items-center px-5 py-4 text-white no-underline hover:bg-red-700 transition duration-200">

                <span class="text-base">
                    ⚙️
                </span>

                <span class="ml-3">
                    Settings
                </span>

            </a>

        </li>

    </ul>

</div>
