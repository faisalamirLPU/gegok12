<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Helpers\SiteHelper;

/**
 * Class NavigationController
 *
 * Handles academic year navigation and selection for admin users.
 * Responsible for fetching academic year lists and updating
 * the currently selected academic year in cache.
 *
 * @package App\Http\Controllers\Admin
 */
class NavigationController extends Controller
{
    /**
     * Get the list of academic years for the logged-in user's school.
     *
     * Returns all academic years ordered by name along with
     * the currently active academic year.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $school_id = Auth::user()->school_id;

        $academic_year = AcademicYear::where('school_id', $school_id)
            ->orderBy('name', 'ASC')
            ->get();

        $current_year = SiteHelper::getAcademicYear($school_id);

        return response()->json([
            'academiclist' => $academic_year,
            'current_year' => $current_year,
        ]);
    }

    /**
     * Set the selected academic year in cache.
     *
     * Clears any cached value for the current school and
     * stores the newly selected academic year ID.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $academic_year_id = $request->academic_year_id;
        $school_id = Auth::user()->school_id;
        $cacheKey = "academic_year_for_school_" . $school_id;

        Cache::forget($cacheKey);
        Cache::put($cacheKey, $academic_year_id, env('CACHE_TIME'));

        $current_year = SiteHelper::getAcademicYear($school_id);

        return response()->json([
            'success' => true,
            'message' => 'Academic year updated successfully.',
            'current_year' => $current_year,
        ]);
    }
}
