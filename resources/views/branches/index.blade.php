@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Branches')
@section('page-heading', 'Branches')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Branch Directory</h2>
                <p class="mt-1 text-sm text-slate-500">Set up branches before assigning branch admins in production.</p>
            </div>

            <a href="{{ route('admin.branches.create') }}" class="inline-flex items-center justify-center rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                Add Branch
            </a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.16em] text-slate-500">
                        <tr>
                            <th class="p-4">Branch</th>
                            <th class="p-4">Location</th>
                            <th class="p-4">Admins</th>
                            <th class="p-4">Staff</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($branches as $branch)
                            <tr class="border-t hover:bg-slate-50">
                                <td class="p-4 font-medium text-slate-900">{{ $branch->name }}</td>
                                <td class="p-4 text-slate-700">{{ $branch->location ?: 'Not set' }}</td>
                                <td class="p-4 text-slate-700">{{ $branch->admin_count }}</td>
                                <td class="p-4 text-slate-700">{{ $branch->staff_count }}</td>
                            </tr>
                        @empty
                            <tr class="border-t">
                                <td colspan="4" class="p-6 text-center text-sm text-slate-500">No branches added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
