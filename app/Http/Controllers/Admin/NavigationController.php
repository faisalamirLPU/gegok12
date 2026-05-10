<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NavigationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Academic Year List
    |--------------------------------------------------------------------------
    */

    public function list()
    {
        $school_id = Auth::user()->school_id;

        /*
    |--------------------------------------------------------------------------
    | Academic Year List
    |--------------------------------------------------------------------------
    */

        $academic_years = AcademicYear::where(
            'school_id',
            $school_id
        )
            ->orderBy('id', 'DESC')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Current Academic Year From Cache
    |--------------------------------------------------------------------------
    */

        $cached_year_id = Cache::get(
            'academic_year_for_school_' . $school_id
        );

        $current_year = null;

        if ($cached_year_id) {

            $current_year = AcademicYear::where(
                'id',
                $cached_year_id
            )
                ->where(
                    'school_id',
                    $school_id
                )
                ->first();
        }

        /*
    |--------------------------------------------------------------------------
    | Fallback To Latest Academic Year
    |--------------------------------------------------------------------------
    */

        if (!$current_year) {

    /*
    |--------------------------------------------------------------------------
    | Get Current Academic Year
    |--------------------------------------------------------------------------
    */

    $current_year = AcademicYear::where(
            'school_id',
            $school_id
        )
        ->where(
            'status',
            1
        )
        ->first();

    /*
    |--------------------------------------------------------------------------
    | Fallback If No Current Year Found
    |--------------------------------------------------------------------------
    */

    if (!$current_year) {

        $current_year = AcademicYear::where(
                'school_id',
                $school_id
            )
            ->orderBy('id', 'DESC')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Store Cache
    |--------------------------------------------------------------------------
    */

    if ($current_year) {

        Cache::put(
            'academic_year_for_school_' . $school_id,
            $current_year->id,
            now()->addDays(30)
        );
    }
}

        return response()->json([

            'academiclist' => $academic_years,

            'current_year' => $current_year,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Change Academic Year
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $request->validate([
            'academic_year_id' =>
            'required|integer|exists:academic_years,id',
        ]);

        $academic_year_id =
        $request->academic_year_id;

        $school_id =
        Auth::user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Clear Old Cache
        |--------------------------------------------------------------------------
        */

        Cache::forget(
            'academic_year_for_school_' . $school_id
        );

        /*
        |--------------------------------------------------------------------------
        | Store New Academic Year
        |--------------------------------------------------------------------------
        */

        Cache::put(
            'academic_year_for_school_' . $school_id,
            $academic_year_id,
            now()->addDays(30)
        );

        /*
        |--------------------------------------------------------------------------
        | Current Year
        |--------------------------------------------------------------------------
        */

        $current_year = AcademicYear::find(
            $academic_year_id
        );

        /*
        |--------------------------------------------------------------------------
        | Session
        |--------------------------------------------------------------------------
        */

        session([
            'academic_year_id'   =>
            $academic_year_id,

            'academic_year_name' =>
            optional($current_year)->name,
        ]);

        return response()->json([

            'success'      => true,

            'message'      =>
            'Academic year updated successfully.',

            'current_year' =>
            $current_year,
        ]);
    }
}
