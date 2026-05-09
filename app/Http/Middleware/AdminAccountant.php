<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAccountant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userGroupId = Auth::user()->usergroup_id;

        /*
        |--------------------------------------------------------------------------
        | Allow Access
        |--------------------------------------------------------------------------
        |
        | 1  = Super Admin
        | 3  = School Admin
        | 11 = Accountant
        |
        */

        if (in_array($userGroupId, [1, 3, 11])) {

            return $next($request);

        }

        /*
        |--------------------------------------------------------------------------
        | Other Role Redirects
        |--------------------------------------------------------------------------
        */

        if ($userGroupId == 5) {
            return redirect('/teacher/dashboard');
        }

        if ($userGroupId == 6) {
            return redirect('/student/dashboard');
        }

        if ($userGroupId == 10) {
            return redirect('/receptionist/dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | Unauthorized
        |--------------------------------------------------------------------------
        */

        abort(404);
    }
}
