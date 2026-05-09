<div class="w-full h-full lg:w-48 md:w-48 bg-red-800 text-white">

    <div class="min-h-full header-wrapper-b hidden lg:block md:block ">

        <ul class="list-reset text-sm">

            {{-- SUPER ADMIN MENU --}}
            @if(Auth::check() && Auth::user()->usergroup_id == 1)

                {{-- Dashboard --}}
                <li class="py-3 px-3 {{ Request::segment('2') == 'dashboard' ? 'active' : '' }}">

                    <a href="{{ url('admin/dashboard') }}" class="flex items-center">

                        <span class="mx-3 whitespace-no-wrap">
                            Dashboard
                        </span>

                    </a>

                </li>


                {{-- Schools --}}
                <li class="py-3 px-3 {{ Request::segment('2') == 'schools' ? 'active' : '' }}">

                    <a href="{{ url('admin/schools') }}" class="flex items-center">

                        <span class="mx-3 whitespace-no-wrap">
                            Schools
                        </span>

                    </a>

                </li>


                {{-- Plans --}}
                <li class="py-3 px-3 {{ Request::segment('2') == 'plans' ? 'active' : '' }}">

                    <a href="{{ url('admin/plans') }}" class="flex items-center">

                        <span class="mx-3 whitespace-no-wrap">
                            Plans
                        </span>

                    </a>

                </li>


                {{-- Subscriptions --}}
                <li class="py-3 px-3 {{ Request::segment('2') == 'subscriptions' ? 'active' : '' }}">

                    <a href="{{ url('admin/subscriptions') }}" class="flex items-center">

                        <span class="mx-3 whitespace-no-wrap">
                            Subscriptions
                        </span>

                    </a>

                </li>


                {{-- Payments --}}
                <li class="py-3 px-3 {{ Request::segment('2') == 'payments' ? 'active' : '' }}">

                    <a href="{{ url('admin/payments') }}" class="flex items-center">

                        <span class="mx-3 whitespace-no-wrap">
                            Payments
                        </span>

                    </a>

                </li>


                {{-- Analytics --}}
                <li class="py-3 px-3 {{ Request::segment('2') == 'analytics' ? 'active' : '' }}">

                    <a href="{{ url('admin/analytics') }}" class="flex items-center">

                        <span class="mx-3 whitespace-no-wrap">
                            Analytics
                        </span>

                    </a>

                </li>


                {{-- Settings --}}
                <li class="py-3 px-3 {{ Request::segment('2') == 'settings' ? 'active' : '' }}">

                    <a href="{{ url('admin/settings') }}" class="flex items-center">

                        <span class="mx-3 whitespace-no-wrap">
                            Settings
                        </span>

                    </a>

                </li>


            {{-- NORMAL SCHOOL ERP MENU --}}
            @else

                @include('layouts.admin.menu')

            @endif

        </ul>

    </div>

</div>



{{-- MOBILE SIDEBAR --}}
<div id="res_sidebar"
     class="w-full lg:w-48 md:w-48 admin-sidebar hidden lg:hidden md:hidden res_sidebar ">

    <div class="min-h-full header-wrapper-b lg:hidden md:hidden bg-red-800">

        <ul class="list-reset text-sm">

            {{-- SUPER ADMIN MOBILE MENU --}}
            @if(Auth::check() && Auth::user()->usergroup_id == 1)

                <li class="py-3 px-3">

                    <a href="{{ url('admin/dashboard') }}" class="flex items-center">

                        <span class="mx-3 whitespace-no-wrap">
                            Dashboard
                        </span>

                    </a>

                </li>

                <li class="py-3 px-3">

                    <a href="{{ url('admin/schools') }}" class="flex items-center">

                        <span class="mx-3 whitespace-no-wrap">
                            Schools
                        </span>

                    </a>

                </li>

                <li class="py-3 px-3">

                    <a href="{{ url('admin/subscriptions') }}" class="flex items-center">

                        <span class="mx-3 whitespace-no-wrap">
                            Subscriptions
                        </span>

                    </a>

                </li>

                <li class="py-3 px-3">

                    <a href="{{ url('admin/plans') }}" class="flex items-center">

                        <span class="mx-3 whitespace-no-wrap">
                            Plans
                        </span>

                    </a>

                </li>

            @else

                @include('layouts.admin.menu')

            @endif

        </ul>

    </div>

</div>
