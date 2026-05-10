<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Userprofile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperadminManagementController extends Controller
{
    private function guard(): void
    {
        abort_unless((int) auth()->user()->usergroup_id === User::SITEADMIN_USERGROUP_ID, 404);
    }

    public function schools()
    {
        $this->guard();

        return view('admin.superadmin.schools', [
            'schools' => School::withCount('user')->latest()->paginate(20),
        ]);
    }

    public function createSchool()
    {
        $this->guard();

        return view('admin.superadmin.school-form', ['plans' => Plan::where('is_active', 1)->get()]);
    }

    public function storeSchool(Request $request)
    {
        $this->guard();

        $data = $request->validate([
            'school_name' => 'required|string|max:255|unique:schools,name',
            'school_email' => 'required|email|max:255|unique:schools,email',
            'phone' => 'nullable|string|max:50',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'nullable|string|min:8',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $password = $data['admin_password'] ?: Str::password(10);
        $plan = Plan::findOrFail($data['plan_id']);

        $school = School::create([
            'name' => $data['school_name'],
            'email' => $data['school_email'],
            'phone' => $data['phone'] ?? null,
            'slug' => Str::slug($data['school_name']),
            'status' => 1,
        ]);

        $admin = User::create([
            'school_id' => $school->id,
            'usergroup_id' => User::SCHOOLADMIN_USERGROUP_ID,
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'password' => Hash::make($password),
            'status' => 'active',
            'email_verified' => 1,
            'email_verified_at' => now(),
        ]);

        Userprofile::create([
            'school_id' => $school->id,
            'user_id' => $admin->id,
            'usergroup_id' => User::SCHOOLADMIN_USERGROUP_ID,
            'firstname' => $data['admin_name'],
            'status' => 'active',
        ]);

        Subscription::create([
            'school_id' => $school->id,
            'user_id' => $admin->id,
            'plan_id' => $plan->id,
            'status' => 'approve',
            'end_date' => now()->addDays((int) $plan->cycle)->toDateString(),
            'payment_details' => ['mode' => 'manual', 'status' => 'approved', 'amount' => $plan->amount],
        ]);

        return redirect('/admin/schools')->with('successmessage', 'School created. Admin password: '.$password);
    }

    public function plans()
    {
        $this->guard();

        return view('admin.superadmin.plans', [
            'plans' => Plan::withCount('subscription')->orderBy('order')->paginate(20),
        ]);
    }

    public function createPlan()
    {
        $this->guard();

        return view('admin.superadmin.plan-form', ['plan' => new Plan()]);
    }

    public function storePlan(Request $request)
    {
        $this->guard();
        Plan::create($this->planData($request));

        return redirect('/admin/plans')->with('successmessage', 'Plan created successfully.');
    }

    public function editPlan(Plan $plan)
    {
        $this->guard();

        return view('admin.superadmin.plan-form', compact('plan'));
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $this->guard();
        $plan->update($this->planData($request));

        return redirect('/admin/plans')->with('successmessage', 'Plan updated successfully.');
    }

    public function approveSubscription(Subscription $subscription)
    {
        $this->guard();
        $subscription->update([
            'status' => 'approve',
            'payment_details' => array_merge($subscription->payment_details ?? [], ['status' => 'approved']),
        ]);

        return back()->with('successmessage', 'Subscription approved.');
    }

    private function planData(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'no_of_members' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        return $data + [
            'cycle' => 30,
            'order' => 1,
            'no_of_events' => 999999,
            'no_of_folders' => 999999,
            'no_of_files' => 999999,
            'no_of_videos' => 999999,
            'no_of_audios' => 999999,
            'no_of_bulletins' => 999999,
            'no_of_groups' => 999999,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
