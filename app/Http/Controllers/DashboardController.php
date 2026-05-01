<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Member;

class DashboardController extends Controller
{
     public function dashboard()
    {
        $user = auth()->user();
        $recentMembers = Member::with(['creator', 'signature.creator'])->latest()->take(5)->get();
        $stats = [];
        $branchPerformance = collect();

        if ($user->isCentralAdmin()) {
            // Central admin sees oversight metrics rather than branch operations.
            $stats = [
                ['label' => 'Your Actions', 'value' => ActivityLog::where('user_id', $user->id)->count(), 'detail' => 'Everything you have done in the system'],
                ['label' => 'Users You Created', 'value' => $user->createdUsers()->count(), 'detail' => 'Branch admins assigned by you'],
                ['label' => 'Admin Activity', 'value' => ActivityLog::where('user_role', 'admin')->orWhere(fn ($query) => $query->whereNull('user_role')->whereHas('user', fn ($inner) => $inner->where('role', 'admin')))->count(), 'detail' => 'Actions performed by branch admins'],
                ['label' => 'Staff Activity', 'value' => ActivityLog::where('user_role', 'staff')->orWhere(fn ($query) => $query->whereNull('user_role')->whereHas('user', fn ($inner) => $inner->where('role', 'staff')))->count(), 'detail' => 'Actions performed by staff across branches'],
            ];

            // This summary is intentionally branch-first: central admin needs to
            // quickly compare branch health without opening each branch manually.
            $branchPerformance = Branch::query()
                ->withCount([
                    'users as admin_count' => fn ($query) => $query->where('role', 'admin'),
                    'users as staff_count' => fn ($query) => $query->where('role', 'staff'),
                ])
                ->get()
                ->map(function (Branch $branch) {
                    return [
                        'name' => $branch->name,
                        'location' => $branch->location,
                        'admin_count' => $branch->admin_count,
                        'staff_count' => $branch->staff_count,
                        'member_count' => Member::whereHas('creator', fn ($query) => $query->where('branch_id', $branch->id))
                            ->orWhereHas('signature.creator', fn ($query) => $query->where('branch_id', $branch->id))
                            ->count(),
                        'recent_activity_count' => ActivityLog::where(function ($query) use ($branch) {
                                $query->where('user_branch_id', $branch->id)
                                    ->orWhere(fn ($legacyQuery) => $legacyQuery->whereNull('user_branch_id')->whereHas('user', fn ($userQuery) => $userQuery->where('branch_id', $branch->id)));
                            })
                            ->where('created_at', '>=', now()->subDays(30))
                            ->count(),
                    ];
                })
                ->sortByDesc('member_count')
                ->values();
        } elseif ($user->isBranchAdmin()) {
            // Branch admin sees team supervision metrics only.
            $stats = [
                ['label' => 'Staff You Created', 'value' => $user->createdUsers()->where('role', 'staff')->count(), 'detail' => 'Staff under your branch'],
                ['label' => 'Members Created By Your Staff', 'value' => Member::whereHas('creator', fn ($query) => $query->where('created_by', $user->id)->where('role', 'staff'))->count(), 'detail' => 'Cards added by your team'],
                ['label' => 'Members In System', 'value' => Member::whereHas('creator', fn ($query) => $query->where('branch_id', $user->branch_id))->orWhereHas('signature.creator', fn ($query) => $query->where('branch_id', $user->branch_id))->count(), 'detail' => 'Visible records you can review'],
            ];
        } else {
            // Staff dashboard stays personal and operationally simple.
            $stats = [
                ['label' => 'Members You Created', 'value' => $user->membersCreated()->count(), 'detail' => 'Cards you personally added'],
                ['label' => 'Members In System', 'value' => Member::whereHas('creator', fn ($query) => $query->where('branch_id', $user->branch_id))->orWhereHas('signature.creator', fn ($query) => $query->where('branch_id', $user->branch_id))->count(), 'detail' => 'Records available for lookup'],
            ];
        }

        return view('dashboard', compact('recentMembers', 'stats', 'branchPerformance'));
    }
}
