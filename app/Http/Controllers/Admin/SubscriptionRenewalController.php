<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionRenewalController extends Controller
{
    public function show()
    {
        return view('admin.core.subscription.renew', [
            'plans' => Plan::where('is_active', 1)->orderBy('amount')->get(),
            'subscription' => Subscription::where('school_id', Auth::user()->school_id)->latest()->first(),
            'school' => Auth::user()->school,
        ]);
    }

    public function requestRenewal(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        $current = Subscription::where('school_id', Auth::user()->school_id)->latest()->first();
        if ($current && $current->status === 'pending') {
            return back()->with('errormessage', 'A renewal request is already pending approval.');
        }

        $plan = Plan::findOrFail($data['plan_id']);

        Subscription::create([
            'school_id' => Auth::user()->school_id,
            'user_id' => Auth::id(),
            'plan_id' => $plan->id,
            'status' => 'pending',
            'end_date' => now()->addDays((int) $plan->cycle)->toDateString(),
            'payment_details' => [
                'mode' => 'qr',
                'status' => 'pending',
                'amount' => $plan->amount,
                'txnid' => $data['transaction_reference'] ?? '',
                'addedon' => now()->toDateTimeString(),
            ],
        ]);

        return back()->with('successmessage', 'Renewal request submitted. Super admin can approve after payment verification.');
    }
}
