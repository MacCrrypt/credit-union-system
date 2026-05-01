<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isCentralAdmin()) {
            $users = User::with(['branch', 'creator'])
                ->withCount(['createdUsers', 'membersCreated'])
                ->whereIn('role', ['admin', 'staff'])
                ->latest()
                ->get();
            $heading = 'User Directory';
            $buttonLabel = 'Add Branch Admin';
            $showBranch = true;
            $showCreator = true;
            $showUserCounts = true;
            $showMemberCounts = true;
            $summaryStats = [
                ['label' => 'Users You Created', 'value' => $user->createdUsers()->count()],
                ['label' => 'Disabled Accounts', 'value' => User::where('status', 'disabled')->count()],
                ['label' => 'Your Actions', 'value' => ActivityLog::where('user_id', $user->id)->count()],
                ['label' => 'Admin Activity', 'value' => ActivityLog::where('user_role', 'admin')->orWhere(fn ($query) => $query->whereNull('user_role')->whereHas('user', fn ($inner) => $inner->where('role', 'admin')))->count()],
            ];
        } else {
            $users = User::with(['branch', 'creator'])
                ->withCount(['createdUsers', 'membersCreated'])
                ->where('role', 'staff')
                ->where('branch_id', $user->branch_id)
                ->latest()
                ->get();
            $heading = 'Staff Directory';
            $buttonLabel = 'Add Staff';
            $showBranch = false;
            $showCreator = false;
            $showUserCounts = false;
            $showMemberCounts = true;
            $summaryStats = [
                ['label' => 'Branch Staff', 'value' => $users->count()],
                ['label' => 'Active Staff', 'value' => $users->where('status', 'active')->count()],
                ['label' => 'Disabled Staff', 'value' => $users->where('status', 'disabled')->count()],
                ['label' => 'Members Created By Staff', 'value' => $users->sum('members_created_count')],
            ];
        }

        return view('users.index', compact(
            'users',
            'heading',
            'buttonLabel',
            'showBranch',
            'showCreator',
            'showUserCounts',
            'showMemberCounts',
            'summaryStats'
        ));
    }

    public function editPassword(User $managedUser)
    {
        $user = auth()->user();

        abort_unless($user->canResetPasswordFor($managedUser), 403);

        return view('users.reset-password', compact('managedUser'));
    }

    public function create()
    {
        $branches = auth()->user()->isCentralAdmin()
            ? Branch::orderBy('name')->get()
            : collect();

        return view('users.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->isCentralAdmin()) {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
                'branch_id' => 'required|exists:branches,id',
            ], [
                'email.unique' => 'That email address is already in use.',
            ]);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'status' => 'active',
                'must_change_password' => true,
                'branch_id' => $request->branch_id,
                'created_by' => $user->id,
            ];
        } else {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            ], [
                'email.unique' => 'That email address is already in use.',
            ]);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'staff',
                'status' => 'active',
                'must_change_password' => true,
                'branch_id' => $user->branch_id,
                'created_by' => $user->id,
            ];
        }

        $createdUser = User::create($data);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'CREATE_USER',
            'description' => 'Created user ' . $createdUser->email . ' with role ' . $createdUser->role,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    public function updatePassword(Request $request, User $managedUser)
    {
        $user = auth()->user();

        abort_unless($user->canResetPasswordFor($managedUser), 403);

        $request->validate([
            // Require the acting admin's current password to reduce damage from
            // an unattended but still-authenticated browser session.
            'current_password' => ['required', 'current_password'],
        ]);

        $temporaryPassword = $this->generateTemporaryPassword();

        $managedUser->update([
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
            'password_changed_at' => null,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'RESET_PASSWORD',
            'description' => 'Issued temporary password for user ' . $managedUser->email,
        ]);

        return redirect()->route('admin.users.password.confirm', $managedUser)
            ->with('success', 'Temporary password generated for ' . $managedUser->name . '. They must change it on next login.')
            ->with('temporary_password', $temporaryPassword);
    }

    public function confirmPassword(User $managedUser)
    {
        if (! session()->has('temporary_password')) {
            return redirect()->route('admin.users.index');
        }

        return view('users.password-reset-confirmed', [
            'managedUser' => $managedUser,
            'temporaryPassword' => session('temporary_password'),
        ]);
    }

    public function editStatus(User $managedUser)
    {
        $user = auth()->user();

        abort_unless($user->canDisableUser($managedUser) || $user->canEnableUser($managedUser), 403);

        return view('users.status', compact('managedUser'));
    }

    public function updateStatus(Request $request, User $managedUser)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'action' => ['required', 'in:disable,enable'],
            'current_password' => ['required', 'current_password'],
            'status_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['action'] === 'disable') {
            abort_unless($user->canDisableUser($managedUser), 403);

            $managedUser->update([
                'status' => 'disabled',
                'status_changed_at' => now(),
                'status_changed_by' => $user->id,
                'status_reason' => $validated['status_reason'] ?? null,
                'remember_token' => null,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'DISABLE_USER',
                'description' => 'Disabled user ' . $managedUser->email . ' with role ' . $managedUser->role,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User disabled successfully. Their records and audit history remain intact.');
        }

        abort_unless($user->canEnableUser($managedUser), 403);

        $managedUser->update([
            'status' => 'active',
            'status_changed_at' => now(),
            'status_changed_by' => $user->id,
            'status_reason' => $validated['status_reason'] ?? null,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'ENABLE_USER',
            'description' => 'Reactivated user ' . $managedUser->email . ' with role ' . $managedUser->role,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User reactivated successfully.');
    }

    public function activityLogs(Request $request)
    {
        $user = auth()->user();

        if ($user->isStaff()) {
            abort(403);
        }

        $filter = $request->get('filter', 'all');
        $branchFilter = $request->get('branch', 'all');
        $query = $this->buildActivityLogQuery($user, $filter, $branchFilter);

        $logs = $query->latest()->paginate(25);

        // Reuse the scoped visibility query for filter options so branch admins
        // only see action types that exist inside their allowed log scope.
        $actions = $this->buildActivityLogQuery($user, 'all', $branchFilter)
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $branches = $user->isCentralAdmin() ? Branch::orderBy('name')->get() : collect();

        return view('admin.activity-logs', compact('logs', 'actions', 'filter', 'branches', 'branchFilter'));
    }

    public function exportActivityLogs(Request $request)
    {
        $user = auth()->user();

        if ($user->isStaff()) {
            abort(403);
        }

        $filter = $request->get('filter', 'all');
        $branchFilter = $request->get('branch', 'all');
        $query = $this->buildActivityLogQuery($user, $filter, $branchFilter);
        $logs = $query->latest()->get();
        $filename = 'activity-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');

            // A streamed response writes rows directly to the output buffer.
            // That keeps memory usage low even if the export grows over time.
            fputcsv($handle, ['User', 'Role', 'Branch', 'Action', 'Description', 'Created At']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->actorName(),
                    str_replace('_', ' ', $log->actorRole()),
                    $log->actorBranchName() ?? 'N/A',
                    $log->action,
                    $log->description,
                    optional($log->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Build one activity-log query that both the page view and CSV export can reuse.
     *
     * Why this is structured this way:
     * - central admin can inspect all logs, optionally narrowed to a branch
     * - branch admin can inspect staff logs for their own branch
     * - action filtering is applied last so the same base access rules always hold
     */
    private function buildActivityLogQuery(User $user, string $filter, string $branchFilter): Builder
    {
        // Eager load the related user and branch to avoid N+1 queries in the table/export loop.
        $query = ActivityLog::with('user.branch');

        if ($user->isCentralAdmin()) {
            // Central admin has institution-wide visibility, so branch is an optional filter only.
            if ($branchFilter !== 'all') {
                $query->where(function ($q) use ($branchFilter) {
                    $q->where('user_branch_id', $branchFilter)
                        ->orWhere(function ($legacyQuery) use ($branchFilter) {
                            $legacyQuery->whereNull('user_branch_id')
                                ->whereHas('user', function ($userQuery) use ($branchFilter) {
                                    $userQuery->where('branch_id', $branchFilter);
                                });
                        });
                });
            }
        } else {
            // Branch admins do not see every log in the system; their audit view stays branch-scoped.
            $query->where(function ($q) use ($user) {
                $q->where(function ($snapshotQuery) use ($user) {
                    $snapshotQuery->where('user_role', 'staff')
                        ->where('user_branch_id', $user->branch_id);
                })->orWhere(function ($legacyQuery) use ($user) {
                    $legacyQuery->whereNull('user_role')
                        ->whereHas('user', function ($userQuery) use ($user) {
                            $userQuery->where('role', 'staff')
                                ->where('branch_id', $user->branch_id);
                        });
                });
            });
        }

        if ($filter !== 'all') {
            $query->where('action', $filter);
        }

        return $query;
    }

    private function generateTemporaryPassword(): string
    {
        $sets = [
            'ABCDEFGHJKLMNPQRSTUVWXYZ',
            'abcdefghijkmnopqrstuvwxyz',
            '23456789',
            '!@#$%^&*',
        ];

        $password = array_map(fn (string $set) => $set[random_int(0, strlen($set) - 1)], $sets);
        $pool = implode('', $sets);

        while (count($password) < 16) {
            $password[] = $pool[random_int(0, strlen($pool) - 1)];
        }

        // Shuffle with random_int so the guaranteed character classes are not predictable by position.
        for ($i = count($password) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$password[$i], $password[$j]] = [$password[$j], $password[$i]];
        }

        return implode('', $password);
    }
}
