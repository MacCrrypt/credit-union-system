@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Add Branch')
@section('page-heading', 'Add Branch')

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
            <form method="POST" action="{{ route('admin.branches.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Branch Name</label>
                    <input id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Bamenda Branch" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                </div>

                <div>
                    <label for="location" class="mb-2 block text-sm font-medium text-slate-700">Location</label>
                    <input id="location" name="location" value="{{ old('location') }}" placeholder="e.g. Bamenda" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="inline-flex rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Create Branch</button>
                    <a href="{{ route('admin.branches.index') }}" class="inline-flex rounded-3xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
