@extends('layouts.admin.layout')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="admin-h1 font-plex my-3">Examinations</h1>
            <p class="text-gray-500 mb-4">Create class-wise exams, upload subject marks, and print marksheets.</p>
        </div>
        @if((int) auth()->user()->usergroup_id !== \App\Models\User::TEACHER_USERGROUP_ID)
            <a href="{{ url('/admin/exams/create') }}" class="bg-red-700 text-white px-4 py-2 rounded">Create Exam</a>
        @endif
    </div>
    @include('partials.message')

    <div class="bg-white custom-shadow border overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-3 px-4">Exam</th>
                    <th class="text-left py-3 px-4">Class</th>
                    <th class="text-left py-3 px-4">Date</th>
                    <th class="text-left py-3 px-4">Status</th>
                    <th class="text-left py-3 px-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $exam->name }}</td>
                        <td class="py-3 px-4">{{ optional($exam->standardLink)->StandardSection ?? '-' }}</td>
                        <td class="py-3 px-4">{{ optional($exam->exam_date)->format('d-m-Y') ?? '-' }}</td>
                        <td class="py-3 px-4">{{ ucwords($exam->status) }}</td>
                        <td class="py-3 px-4"><a class="text-blue-600" href="{{ url((request()->segment(1) === 'teacher' ? '/teacher' : '/admin').'/exams/'.$exam->id) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 px-4 text-center text-gray-500">No exams found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $exams->links() }}</div>
</div>
@endsection
