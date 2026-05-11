<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.finance.dashboard.index');
    }
}
