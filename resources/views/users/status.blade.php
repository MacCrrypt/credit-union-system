@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Account Status')
@section('page-heading', 'Account Status')

@section('content')
    <div class="space-y-6">
        @if ($errors->any())
            <div class="rounded-3xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
                <p class="mb-2 font-semibold">Please fix the following errors:</p>
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-3xl border {{ $managedUser->isActive() ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-emerald-200 bg-emerald-50 text-emerald-900' }} p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.2em]">{{ $managedUser->isActive() ? 'Disable account' : 'Reactivate account' }}</p>
            <h2 class="mt-3 text-2xl font-semibold">{{ $managedUser->isActive() ? 'Pause access without deleting audit history.' : 'Restore access for this user.' }}</h2>
            <p class="mt-2 text-sm leading-6">The account record, member ownership, and activity logs stay in place. This is better for leave, transfers, and audits than deleting the user.</p>
        </div>

        <div class="max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <dl class="grid gap-5 md:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">Name</dt>
                    <dd class="mt-2 text-base font-semibold text-slate-900">{{ $managedUser->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">Email</dt>
                    <dd class="mt-2 text-base font-semibold text-slate-900">{{ $managedUser->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">Role</dt>
                    <dd class="mt-2 text-base font-semibold text-slate-900">{{ str_replace('_', ' ', $managedUser->role) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">Current Status</dt>
                    <dd class="mt-2 text-base font-semibold text-slate-900">{{ ucfirst($managedUser->status ?? 'active') }}</dd>
                </div>
            </dl>

            <form method="POST" action="{{ route('admin.users.status.update', $managedUser) }}" class="mt-8 space-y-5">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="{{ $managedUser->isActive() ? 'disable' : 'enable' }}">

                <div>
                    <label for="status_reason" class="mb-2 block text-sm font-medium text-slate-700">Reason</label>
                    <textarea id="status_reason" name="status_reason" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" placeholder="Leave, branch transfer, return from leave, or another approved reason.">{{ old('status_reason') }}</textarea>
                </div>

                <div>
                    <label for="current_password" class="mb-2 block text-sm font-medium text-slate-700">Your Current Password</label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                    <p class="mt-1 text-xs text-slate-500">This confirms the status change is being performed intentionally by the signed-in admin.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex rounded-3xl {{ $managedUser->isActive() ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700' }} px-6 py-3 text-sm font-semibold text-white transition">
                        {{ $managedUser->isActive() ? 'Disable Account' : 'Reactivate Account' }}
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex rounded-3xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
