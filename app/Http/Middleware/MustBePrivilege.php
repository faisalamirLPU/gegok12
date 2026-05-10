<?php

namespace App\Http\Middleware;

use App\Helpers\SiteHelper;
use App\Models\Standard;
use App\Models\Subscription;
use App\Models\User;
use Closure;

class MustBePrivilege
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
        if((int) \Auth::user()->usergroup_id === User::SITEADMIN_USERGROUP_ID)
        {
            return $next($request);
        }

        if (!$request->is('admin/subscription/renew*')) {
            $subscription = Subscription::where('school_id', \Auth::user()->school_id)->latest()->first();

            $isExpired = $subscription && ($subscription->status === 'expired' || ($subscription->end_date && now()->startOfDay()->gt($subscription->end_date)));
            $isPending = $subscription && $subscription->status === 'pending';

            if ($isExpired || $isPending) {
                if ($isExpired) {
                    $subscription->update(['status' => 'expired']);
                }

                return redirect('/admin/subscription/renew')
                    ->with('errormessage', 'Your school subscription is not active. Renewed plans are only active after super admin approval.');
            }
        }

        $academic_year = SiteHelper::getAcademicYear(\Auth::user()->school_id);
        if($academic_year != null)
        {
            $standard_count = Standard::where('school_id',\Auth::user()->school_id)->count();

            if($standard_count > 0)
            {
                return $next($request);
            }
            else
            {
                return redirect('/admin/standard/create');
            }
        }
        else
        {
            return redirect('/admin/academics');
        }

        abort(404);
    }
}
