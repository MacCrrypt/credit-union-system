@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' — Activity Logs')
@section('page-heading', 'Activity Logs')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4">
                <p class="text-sm font-semibold text-slate-700 mb-3">Filter by Action</p>
                <form method="GET" class="flex flex-wrap gap-2">
                    <input type="hidden" name="branch" value="{{ $branchFilter }}">
                    <a href="{{ route('admin.activity-logs', ['filter' => 'all', 'branch' => $branchFilter]) }}" class="inline-flex rounded-3xl px-4 py-2 text-sm font-semibold transition {{ $filter === 'all' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        All Activities
                    </a>
                    @foreach($actions as $action)
                        <a href="{{ route('admin.activity-logs', ['filter' => $action, 'branch' => $branchFilter]) }}" class="inline-flex rounded-3xl px-4 py-2 text-sm font-semibold transition {{ $filter === $action ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ str_replace('_', ' ', $action) }}
                        </a>
                    @endforeach
                </form>
            </div>

            @if($branches->isNotEmpty())
            <div class="mb-4">
                <p class="text-sm font-semibold text-slate-700 mb-3">Filter by Branch</p>
                <form method="GET" class="flex flex-wrap gap-2">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <a href="{{ route('admin.activity-logs', ['filter' => $filter, 'branch' => 'all']) }}" class="inline-flex rounded-3xl px-4 py-2 text-sm font-semibold transition {{ $branchFilter === 'all' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        All Branches
                    </a>
                    @foreach($branches as $branch)
                        <a href="{{ route('admin.activity-logs', ['filter' => $filter, 'branch' => $branch->id]) }}" class="inline-flex rounded-3xl px-4 py-2 text-sm font-semibold transition {{ $branchFilter == $branch->id ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ $branch->name }}
                        </a>
                    @endforeach
                </form>
            </div>
            @endif
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-[0.16em] text-xs">
                        <tr>
                            <th class="p-4">Staff Member</th>
                            <th class="p-4">Action</th>
                            <th class="p-4">Description</th>
                            <th class="p-4">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="border-t hover:bg-slate-50">
                                <td class="p-4 font-medium text-slate-900">
                                    {{ $log->user->name }}
                                    @if($log->user->branch)
                                        <div class="mt-1 text-xs text-slate-500">{{ $log->user->branch->name }}</div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        {{ str_replace('_', ' ', $log->action) }}
                                    </span>
                                </td>
                                <td class="p-4 text-slate-700">{{ $log->description ?? '—' }}</td>
                                <td class="p-4 text-slate-600 text-xs">
                                    {{ $log->created_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-slate-500">No activity logs found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($logs->hasPages())
            <div class="flex justify-center">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection