@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' — ' . (auth()->user()->isCentralAdmin() ? 'Add Branch Admin' : 'Add Staff'))
@section('page-heading', auth()->user()->isCentralAdmin() ? 'Add Branch Admin' : 'Add Staff')

@section('content')
    <div class="space-y-6">
        @if ($errors->any())
            <div class="rounded-3xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
                <p class="font-semibold mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm max-w-2xl">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                @csrf

                <input name="name" value="{{ old('name') }}" placeholder="Name" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                <input name="email" value="{{ old('email') }}" placeholder="Email" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                <input name="password" type="password" placeholder="Password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />

                <p class="text-sm text-slate-500">Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.</p>

                <input type="hidden" name="role" value="{{ auth()->user()->isCentralAdmin() ? 'admin' : 'staff' }}" />

                @if(auth()->user()->isCentralAdmin())
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700" for="branch_id">Branch</label>
                        <select name="branch_id" id="branch_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none">
                            <option value="">Select branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }} ({{ $branch->location ?? 'No location' }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button class="inline-flex rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Create</button>
            </form>
        </div>
    </div>
@endsection
