<?php

namespace App\Traits\Finance;

use App\Helpers\SiteHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToAcademicYear
{
    protected static function bootBelongsToAcademicYear()
    {
        static::creating(function ($model) {

            if (Auth::check() && empty($model->academic_year_id)) {

                $academicYear = SiteHelper::getAcademicYear(
                    Auth::user()->school_id
                );

                if ($academicYear) {

                    $model->academic_year_id = $academicYear->id;
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCurrentAcademicYear(Builder $query)
    {
        if (Auth::check()) {

            $academicYear = SiteHelper::getAcademicYear(
                Auth::user()->school_id
            );

            if ($academicYear) {

                return $query->where(
                    'academic_year_id',
                    $academicYear->id
                );
            }
        }

        return $query;
    }
}
