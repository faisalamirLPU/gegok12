<?php

/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\AuthenticatesUsers;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller implements ShouldQueue
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to their respective dashboards based on roles.
    |
    */

    use AuthenticatesUsers;

    /**
     * Redirect users after login based on usergroup_id.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $user = Auth::user();

        if (!$user) {
            return '/login';
        }

        switch ((int) $user->usergroup_id) {

            // Super Admin / Site Admin
            case 1:
                return '/admin/dashboard';

            // Site Subadmin
            case 2:
                return '/admin/dashboard';

            // School Admin
            case 3:
                return '/admin/dashboard';

            // School Subadmin
            case 4:
                return '/admin/dashboard';

            // Teacher
            case 5:
                return '/teacher/dashboard';

            // Student
            case 6:
                return '/student/dashboard';

            // Parent
            case 7:
                return '/parent/dashboard';

            // Librarian
            case 8:
                return '/librarian/dashboard';

            // Old Student
            case 9:
                return '/student/dashboard';

            // Receptionist
            case 10:
                return '/receptionist/dashboard';

            // Accountant
            case 11:
                return '/accountant/dashboard';

            // Stock Keeper
            case 12:
                return '/stock/dashboard';

            // Non Teaching
            case 13:
                return '/admin/dashboard';

            // Default fallback
            default:
                return '/';
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }
}
