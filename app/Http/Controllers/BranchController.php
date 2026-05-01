<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        // Branch setup is a central-admin responsibility because branch records
        // affect user assignment and institution-wide reporting.
        abort_unless(auth()->user()->isCentralAdmin(), 403);

        $branches = Branch::withCount([
            'users as admin_count' => fn ($query) => $query->where('role', 'admin'),
            'users as staff_count' => fn ($query) => $query->where('role', 'staff'),
        ])->orderBy('name')->get();

        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isCentralAdmin(), 403);

        return view('branches.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isCentralAdmin(), 403);

        $validated = $request->validate([
            // Branch names are kept unique so user assignment and reporting stay unambiguous.
            'name' => ['required', 'string', 'max:255', 'unique:branches,name'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        Branch::create($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch created successfully.');
    }
}
