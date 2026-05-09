<div class="w-full bg-white shadow-sm px-6 py-4 flex justify-between items-center">

    {{-- Left --}}
    <div class="flex items-center gap-4">

        <div class="w-12 h-12 bg-red-700 rounded-xl flex items-center justify-center text-white text-2xl font-bold">
            G
        </div>

        <div>

            <h1 class="text-2xl font-bold text-gray-800">
                GegoK12 SaaS
            </h1>

            <p class="text-sm text-gray-500">
                Super Administrator Panel
            </p>

        </div>

    </div>


    {{-- Right --}}
    <div class="flex items-center gap-6">

        <button class="text-2xl text-gray-600">
            🔔
        </button>

        <div class="flex items-center gap-3">

            <div class="w-10 h-10 rounded-full bg-red-700 text-white flex items-center justify-center font-bold">
                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
            </div>

            <div>

                <p class="font-semibold text-gray-800">
                    {{ Auth::user()->name }}
                </p>

                <p class="text-sm text-gray-500">
                    Super Admin
                </p>

            </div>

        </div>

    </div>

</div>
