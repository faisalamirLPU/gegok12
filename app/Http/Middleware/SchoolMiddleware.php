<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\School;

class SchoolMiddleware
{
    /**
     * Handle request.
     */
    public function handle(
        Request $request,
        Closure $next
    ) {

        $slug = $request->route('school');

        /*
        |--------------------------------------------------------------------------
        | Find School
        |--------------------------------------------------------------------------
        */

        $school = School::where(
            'slug',
            $slug
        )->first();

        /*
        |--------------------------------------------------------------------------
        | Invalid School
        |--------------------------------------------------------------------------
        */

        if (!$school) {

            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Store Current School
        |--------------------------------------------------------------------------
        */

        app()->instance(
            'current_school',
            $school
        );

        return $next($request);
    }
}
