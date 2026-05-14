<?php

namespace App\Http\Controllers\Admin\Audit;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditTrail::with('user.userprofile')->latest();

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $trails = $query->paginate(50);
        
        $actionTypes = AuditTrail::select('action_type')->distinct()->pluck('action_type');

        return view('admin.audit.index', compact('trails', 'actionTypes'));
    }

    public function show($id)
    {
        $trail = AuditTrail::with('user.userprofile')->findOrFail($id);
        
        return view('admin.audit.show', compact('trail'));
    }
}
