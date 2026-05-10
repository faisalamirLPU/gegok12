@extends('layouts.admin.layout')

@section('content')
<div>
    <h1 class="admin-h1 font-plex my-3">Settings</h1>
    <p class="text-gray-500 mb-4">Platform settings and master data.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <a href="{{ url('admin/setting/countries') }}" class="block bg-white custom-shadow border px-5 py-4 hover:bg-gray-50">
            <p class="font-semibold text-gray-800">Countries</p>
            <p class="text-sm text-gray-500 mt-1">Manage country master records.</p>
        </a>
        <a href="{{ url('admin/setting/states') }}" class="block bg-white custom-shadow border px-5 py-4 hover:bg-gray-50">
            <p class="font-semibold text-gray-800">States</p>
            <p class="text-sm text-gray-500 mt-1">Manage state master records.</p>
        </a>
        <a href="{{ url('admin/setting/cities') }}" class="block bg-white custom-shadow border px-5 py-4 hover:bg-gray-50">
            <p class="font-semibold text-gray-800">Cities</p>
            <p class="text-sm text-gray-500 mt-1">Manage city master records.</p>
        </a>
        <a href="{{ url('admin/setting/smstemplates') }}" class="block bg-white custom-shadow border px-5 py-4 hover:bg-gray-50">
            <p class="font-semibold text-gray-800">SMS Templates</p>
            <p class="text-sm text-gray-500 mt-1">Manage platform SMS templates.</p>
        </a>
        <a href="{{ url('admin/settings/generalsettings') }}" class="block bg-white custom-shadow border px-5 py-4 hover:bg-gray-50">
            <p class="font-semibold text-gray-800">General Settings</p>
            <p class="text-sm text-gray-500 mt-1">Manage site title, logo, and app settings.</p>
        </a>
    </div>
</div>
@endsection
