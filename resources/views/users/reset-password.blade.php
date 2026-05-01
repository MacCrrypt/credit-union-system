@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Reset Password')
@section('page-heading', 'Reset Password')

@section('content')
    <div class="space-y-6">
        @if ($errors->any())
            <div class="rounded-3xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
                <p class="mb-2 font-semibold">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="max-w-2xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Reset password for {{ $managedUser->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $managedUser->email }}</p>
            </div>

            <form method="POST" action="{{ route('admin.users.password.update', $managedUser) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="mb-2 block text-sm font-medium text-slate-700">Your Current Password</label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                    <p class="mt-1 text-xs text-slate-500">This confirms the action is being performed intentionally by the signed-in admin.</p>
                </div>

                <div class="rounded-3xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    The system will generate a temporary password and force {{ $managedUser->name }} to change it on next login.
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Generate Temporary Password</button>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex rounded-3xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
