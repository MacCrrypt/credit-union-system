@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Temporary Password Generated')
@section('page-heading', 'Temporary Password Generated')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-900">Temporary password created for {{ $managedUser->name }}</h2>
            <p class="mt-1 text-sm text-emerald-700">{{ $managedUser->email }}</p>

            <div class="mt-6 rounded-3xl border border-amber-200 bg-white p-5">
                <p class="text-sm font-medium text-slate-700">One-time temporary password</p>
                <p class="mt-3 font-mono text-lg font-semibold tracking-wide text-slate-900">{{ $temporaryPassword }}</p>
            </div>

            <p class="mt-4 text-sm leading-6 text-slate-600">Share this securely with the user. The password is only displayed once, and the user will be forced to change it on their next login.</p>
        </div>

        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
            Return to User Directory
        </a>
    </div>
@endsection
