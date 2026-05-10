@extends('layouts.admin.layout')

@section('content')
<div>
    <h1 class="admin-h1 font-plex my-3">Teacher Login Credentials</h1>
    <p class="text-gray-500 mb-4">School admins can reset or generate teacher passwords.</p>
    @include('partials.message')

    <div class="bg-white custom-shadow border overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-3 px-4">Teacher</th>
                    <th class="text-left py-3 px-4">Login Email</th>
                    <th class="text-left py-3 px-4">New Password</th>
                    <th class="text-left py-3 px-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teachers as $teacher)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ optional($teacher->userprofile)->firstname }} {{ optional($teacher->userprofile)->lastname }}</td>
                        <td class="py-3 px-4">{{ $teacher->email }}</td>
                        <td class="py-3 px-4">
                            <form method="POST" action="{{ url('/admin/teacher-credentials/'.$teacher->id.'/reset') }}" class="flex gap-2">
                                @csrf
                                <input name="password" placeholder="Leave blank to auto-generate" class="border px-3 py-2 w-64">
                                <button class="bg-red-700 text-white px-3 py-2 rounded">Reset</button>
                            </form>
                        </td>
                        <td class="py-3 px-4"><a class="text-blue-600" href="{{ url('/admin/teacher/show/'.$teacher->name) }}">Profile</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $teachers->links() }}</div>
</div>
@endsection
