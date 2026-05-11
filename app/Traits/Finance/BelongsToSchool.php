<?php

namespace App\Traits\Finance;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToSchool
{
    protected static function bootBelongsToSchool()
    {
        static::creating(function ($model) {

            if (Auth::check() && empty($model->school_id)) {

                $model->school_id = Auth::user()->school_id;
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCurrentSchool(Builder $query)
    {
        if (Auth::check()) {

            return $query->where(
                'school_id',
                Auth::user()->school_id
            );
        }

        return $query;
    }
}
