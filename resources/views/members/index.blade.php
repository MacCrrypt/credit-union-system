@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Members')
@section('page-heading', 'Members')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" class="flex w-full gap-3 lg:max-w-2xl">
                <label for="searchInput" class="sr-only">Search</label>
                <input id="searchInput" type="text" name="search" value="{{ request('search') }}" placeholder="Search account number or name..." class="min-w-0 flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                <button class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Search</button>
            </form>

            <div class="flex flex-wrap gap-3">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.reports.members.export', ['search' => request('search')]) }}" class="inline-flex items-center justify-center rounded-3xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-emerald-500 hover:text-emerald-600">Export CSV</a>
                @endif

                @if (auth()->user()->canCreateMembers())
                    <a href="{{ route('members.create') }}" class="inline-flex items-center justify-center rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add Member</a>
                @endif
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.16em] text-slate-500">
                        <tr>
                            <th class="p-4">Account</th>
                            <th class="p-4">Name</th>
                            <th class="p-4">Created By</th>
                            <th class="p-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                            <tr class="border-t hover:bg-slate-50">
                                <td class="p-4 font-medium text-slate-900">{{ $member->account_number }}</td>
                                <td class="p-4 text-slate-700">{{ $member->name }}</td>
                                <td class="p-4 text-slate-700">{{ $member->creator?->name ?? 'Unknown' }}</td>
                                <td class="p-4 text-right">
                                    <div class="flex flex-wrap justify-end gap-4">
                                        <a href="{{ route('members.show', $member) }}" class="text-emerald-600 hover:underline">View</a>
                                        @if (auth()->user()->canManageMember($member))
                                            <a href="{{ route('members.edit', $member) }}" class="text-blue-600 hover:underline">Edit Card</a>
                                            <a href="{{ route('members.delete.preview', $member) }}" class="text-red-600 hover:underline">Delete</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t">
                                <td colspan="4" class="p-6 text-center text-sm text-slate-500">No members found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $members->withQueryString()->links() }}
        </div>
    </div>
@endsection
