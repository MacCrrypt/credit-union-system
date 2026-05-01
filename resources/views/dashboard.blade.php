@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Dashboard')
@section('page-heading', 'Dashboard')

@section('content')
    <div class="space-y-6">
        @if (! auth()->user()->isCentralAdmin())
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Search Members</h2>
                        <p class="mt-1 text-sm text-slate-500">Find member accounts by name or account number.</p>
                    </div>

                    <form method="GET" action="{{ route('members.index') }}" class="flex w-full gap-3 md:w-auto">
                        <label for="searchInput" class="sr-only">Search</label>
                        <input id="searchInput" type="text" name="search" placeholder="Search account number or name..." class="min-w-0 flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                        <button class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Search</button>
                    </form>
                </div>
            </section>
        @endif

        @if (! empty($stats))
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($stats as $stat)
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $stat['label'] }}</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stat['value'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $stat['detail'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="grid gap-4 lg:grid-cols-3">
            @if (auth()->user()->canCreateMembers())
                <a href="{{ route('members.create') }}" class="rounded-3xl border border-slate-200 bg-emerald-600 p-5 text-white shadow-sm transition hover:bg-emerald-700">
                    <h3 class="font-semibold">Add Member</h3>
                    <p class="mt-2 text-sm text-emerald-100">Register a new member record</p>
                </a>
            @else
                <div class="rounded-3xl border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
                    <h3 class="font-semibold">Member Creation</h3>
                    <p class="mt-2 text-sm text-slate-500">Only staff can add new members.</p>
                </div>
            @endif

            @if (! auth()->user()->isCentralAdmin())
                <a href="{{ route('members.index') }}" class="rounded-3xl border border-slate-200 bg-white p-5 text-slate-900 shadow-sm transition hover:bg-slate-50">
                    <h3 class="font-semibold">View Members</h3>
                    <p class="mt-2 text-sm text-slate-500">Search and review records</p>
                </a>
            @else
                <div class="rounded-3xl border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
                    <h3 class="font-semibold">Member Records</h3>
                    <p class="mt-2 text-sm text-slate-500">Central admin follows activity and branch summaries instead of opening member cards.</p>
                </div>
            @endif

            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="rounded-3xl border border-slate-200 bg-white p-5 text-slate-900 shadow-sm transition hover:bg-slate-50">
                    <h3 class="font-semibold">Manage Users</h3>
                    <p class="mt-2 text-sm text-slate-500">Review user ownership and permissions</p>
                </a>
            @else
                <div class="rounded-3xl border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
                    <h3 class="font-semibold">System Status</h3>
                    <p class="mt-2 text-sm text-slate-500">Operational</p>
                </div>
            @endif
        </div>

        @if (! auth()->user()->isCentralAdmin())
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-6">
                    <h3 class="text-lg font-semibold text-slate-900">Recent Members</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <tbody>
                            @forelse ($recentMembers as $member)
                                <tr class="border-t hover:bg-slate-50">
                                    <td class="p-3">{{ $member->account_number }}</td>
                                    <td class="p-3">{{ $member->name }}</td>
                                    <td class="p-3 text-slate-500">{{ $member->creator?->name ?? 'Unknown' }}</td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('members.show', $member) }}" class="text-emerald-600 hover:underline">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-t">
                                    <td colspan="4" class="p-6 text-center text-sm text-slate-500">No recent members found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        {{-- Branch performance is only meaningful for the central-admin oversight view. --}}
        @if (auth()->user()->isCentralAdmin() && $branchPerformance->isNotEmpty())
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-6">
                    <h3 class="text-lg font-semibold text-slate-900">Branch Performance</h3>
                    <p class="mt-1 text-sm text-slate-500">Operational picture across branches based on users, member records, and recent activity.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.16em] text-slate-500">
                            <tr>
                                <th class="p-4">Branch</th>
                                <th class="p-4">Admins</th>
                                <th class="p-4">Staff</th>
                                <th class="p-4">Members Created</th>
                                <th class="p-4">Activity (30 Days)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($branchPerformance as $branch)
                                <tr class="border-t hover:bg-slate-50">
                                    <td class="p-4">
                                        <p class="font-medium text-slate-900">{{ $branch['name'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $branch['location'] ?: 'No location set' }}</p>
                                    </td>
                                    <td class="p-4 text-slate-700">{{ $branch['admin_count'] }}</td>
                                    <td class="p-4 text-slate-700">{{ $branch['staff_count'] }}</td>
                                    <td class="p-4 text-slate-700">{{ $branch['member_count'] }}</td>
                                    <td class="p-4 text-slate-700">{{ $branch['recent_activity_count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>
@endsection
