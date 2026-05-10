<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\School;

class AddSchoolSlugToAdminUrl
{
    /**
     * Handle an incoming request.
     *
     * Automatically appends the school slug to admin URLs
     * for better readability and tracking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply to admin routes
        if ($request->segment(1) !== 'admin' || !$request->user()) {
            return $next($request);
        }

        // Skip if school parameter already exists
        if ($request->has('school')) {
            return $next($request);
        }

        // Skip for superadmin (usergroup_id == 1)
        if ($request->user()->usergroup_id == 1) {
            return $next($request);
        }

        // Get the school and create slug
        $school = School::find($request->user()->school_id);
        if ($school) {
            $schoolSlug = \Str::slug($school->name);

            // Redirect to URL with school parameter
            return redirect(
                $request->getPathInfo() . '?' . http_build_query(
                    array_merge($request->query(), ['school' => $schoolSlug])
                )
            );
        }

        return $next($request);
    }
}
