<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherCredentialController extends Controller
{
    public function index()
    {
        return view('admin.core.teachers.credentials', [
            'teachers' => User::with('userprofile')
                ->where('school_id', Auth::user()->school_id)
                ->where('usergroup_id', User::TEACHER_USERGROUP_ID)
                ->orderBy('name')
                ->paginate(25),
        ]);
    }

    public function reset(Request $request, User $teacher)
    {
        abort_unless((int) $teacher->school_id === (int) Auth::user()->school_id, 404);
        abort_unless((int) $teacher->usergroup_id === User::TEACHER_USERGROUP_ID, 404);

        $password = $request->filled('password') ? $request->password : Str::password(10);
        $teacher->update(['password' => Hash::make($password)]);

        return back()->with('successmessage', 'Teacher login updated. New password: '.$password);
    }
}
