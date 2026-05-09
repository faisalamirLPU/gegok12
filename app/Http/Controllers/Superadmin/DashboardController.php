<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {

        $totalSchools = School::count();

        $activeSchools = School::where('status', 1)->count();

        $students = User::where('usergroup_id', 5)->count();

        $teachers = User::where('usergroup_id', 4)->count();

        $subscriptions = Subscription::count();

        $expiredPlans = Subscription::where('status', 'expired')->count();

        $plans = Plan::count();

        $recentSchools = School::latest()->take(5)->get();


        return view('superadmin.dashboard', compact(
            'totalSchools',
            'activeSchools',
            'students',
            'teachers',
            'subscriptions',
            'expiredPlans',
            'plans',
            'recentSchools'
        ));
    }
}
