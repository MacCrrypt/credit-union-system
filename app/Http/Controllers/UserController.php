<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isCentralAdmin()) {
            $users = User::where('role', 'admin')
                ->where('created_by', $user->id)
                ->latest()
                ->get();
            $heading = 'Branch Admin Directory';
            $buttonLabel = 'Add Admin';
            $showBranch = true;
        } else {
            $users = User::where('role', 'staff')
                ->where('branch_id', $user->branch_id)
                ->where('created_by', $user->id)
                ->latest()
                ->get();
            $heading = 'Staff Directory';
            $buttonLabel = 'Add Staff';
            $showBranch = false;
        }

        return view('users.index', compact('users', 'heading', 'buttonLabel', 'showBranch'));
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

    public function activityLogs(Request $request)
    {
        $user = auth()->user();

        if ($user->isStaff()) {
            abort(403);
        }

        $filter = $request->get('filter', 'all');
        $branchFilter = $request->get('branch', 'all');

        $query = ActivityLog::with('user.branch');

        if ($user->isCentralAdmin()) {
            // Central admin sees all logs
            if ($branchFilter !== 'all') {
                $query->whereHas('user', function ($q) use ($branchFilter) {
                    $q->where('branch_id', $branchFilter);
                });
            }
        } else {
            // Branch admin sees logs of staff they created in their branch
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('role', 'staff')
                    ->where('branch_id', $user->branch_id)
                    ->where('created_by', $user->id);
            });
        }

        if ($filter !== 'all') {
            $query->where('action', $filter);
        }

        $logs = $query->latest()->paginate(25);

        $actions = ActivityLog::distinct()->pluck('action');

        $branches = $user->isCentralAdmin() ? Branch::orderBy('name')->get() : collect();

        return view('admin.activity-logs', compact('logs', 'actions', 'filter', 'branches', 'branchFilter'));
    }
}
