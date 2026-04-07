@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' — Members')
@section('page-heading', 'Members')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" class="flex w-full gap-3 lg:max-w-2xl">
                <label for="searchInput" class="sr-only">Search</label>
                <input id="searchInput" type="text" name="search" placeholder="Search account number or name..." class="min-w-0 flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                <button class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Search</button>
            </form>

            <a href="{{ route('members.create') }}" class="inline-flex items-center justify-center rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add Member</a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-[0.16em] text-xs">
                        <tr>
                            <th class="p-4">Account</th>
                            <th class="p-4">Name</th>
                            <th class="p-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            <tr class="border-t hover:bg-slate-50">
                                <td class="p-4 font-medium text-slate-900">{{ $member->account_number }}</td>
                                <td class="p-4 text-slate-700">{{ $member->name }}</td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('members.show', $member) }}" class="text-emerald-600 hover:underline mr-4">View</a>
                                    <a href="{{ route('members.edit', $member) }}" class="text-blue-600 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
