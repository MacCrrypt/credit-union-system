@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Change Temporary Password')
@section('page-heading', 'Change Temporary Password')

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
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

        <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6 text-amber-900 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.2em]">Temporary password</p>
            <h2 class="mt-3 text-2xl font-semibold">Set your own password before continuing.</h2>
            <p class="mt-2 text-sm leading-6">Your administrator reset this account with a temporary password. For accountability, only you should know the password used after this step.</p>
        </div>

        <form method="POST" action="{{ route('password.change.update') }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="mb-2 block text-sm font-medium text-slate-700">Temporary Password</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-slate-700">New Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
            </div>

            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Confirm New Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
            </div>

            <p class="text-sm text-slate-500">Use at least 8 characters with uppercase, lowercase, number, and symbol.</p>

            <button type="submit" class="inline-flex rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Change Password</button>
        </form>
    </div>
@endsection
