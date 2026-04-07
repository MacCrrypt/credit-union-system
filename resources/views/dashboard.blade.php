@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' — Dashboard')
@section('page-heading', 'Dashboard')

@section('content')
    <div class="space-y-6">
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

        <div class="grid gap-4 lg:grid-cols-3">
            <a href="{{ route('members.create') }}" class="rounded-3xl border border-slate-200 bg-emerald-600 p-5 text-white shadow-sm transition hover:bg-emerald-700">
                <h3 class="font-semibold">Add Member</h3>
                <p class="mt-2 text-sm text-emerald-100">Register new member</p>
            </a>

            <a href="{{ route('members.index') }}" class="rounded-3xl border border-slate-200 bg-white p-5 text-slate-900 shadow-sm transition hover:bg-slate-50">
                <h3 class="font-semibold">View Members</h3>
                <p class="mt-2 text-sm text-slate-500">Search all records</p>
            </a>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
                <h3 class="font-semibold">System Status</h3>
                <p class="mt-2 text-sm text-slate-500">Operational</p>
            </div>
        </div>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Recent Members</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <tbody>
                        @foreach ($recentMembers as $member)
                            <tr class="border-t hover:bg-slate-50">
                                <td class="p-3">{{ $member->account_number }}</td>
                                <td class="p-3">{{ $member->name }}</td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('members.show', $member) }}" class="text-emerald-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
