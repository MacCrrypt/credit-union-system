@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' — ' . $heading)
@section('page-heading', $heading)

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">{{ $heading }}</h2>
                <p class="mt-1 text-sm text-slate-500">Manage access and roles for your assigned users.</p>
            </div>

            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                {{ $buttonLabel }}
            </a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                <thead class="bg-slate-50 text-sm uppercase tracking-[0.16em] text-slate-500">
                    <tr>
                        <th class="p-4">Name</th>
                        <th class="p-4">Email</th>
                        @if ($showBranch)
                            <th class="p-4">Branch</th>
                        @endif
                        <th class="p-4">Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-t hover:bg-slate-50">
                            <td class="p-4 font-medium text-slate-900">{{ $user->name }}</td>
                            <td class="p-4 text-slate-700">{{ $user->email }}</td>
                            @if ($showBranch)
                                <td class="p-4 text-slate-700">{{ $user->branch->name ?? '—' }}</td>
                            @endif
                            <td class="p-4 text-slate-700">{{ $user->role }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
