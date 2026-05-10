@extends('layouts.admin.layout')

@section('content')
<div>
    <h1 class="admin-h1 font-plex my-3">Analytics</h1>
    <p class="text-gray-500 mb-4">Platform-wide counts and subscription health.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @include('admin.superadmin.partials.metric-card', ['label' => 'Schools', 'value' => $schoolCount])
        @include('admin.superadmin.partials.metric-card', ['label' => 'Active Schools', 'value' => $activeSchoolCount])
        @include('admin.superadmin.partials.metric-card', ['label' => 'Students', 'value' => $studentCount])
        @include('admin.superadmin.partials.metric-card', ['label' => 'Teachers', 'value' => $teacherCount])
        @include('admin.superadmin.partials.metric-card', ['label' => 'Plans', 'value' => $planCount])
        @include('admin.superadmin.partials.metric-card', ['label' => 'Subscriptions', 'value' => $subscriptionCount])
        @include('admin.superadmin.partials.metric-card', ['label' => 'Active Subscriptions', 'value' => $activeSubscriptionCount])
        @include('admin.superadmin.partials.metric-card', ['label' => 'Expired Subscriptions', 'value' => $expiredSubscriptionCount])
    </div>
</div>
@endsection
