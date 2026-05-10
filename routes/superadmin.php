<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/dashboard', '/admin/dashboard')
    ->name('superadmin.dashboard');
