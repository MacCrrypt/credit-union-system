@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' — Add Member')
@section('page-heading', 'Add Member')

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
            <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="Account Number" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Name" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700" for="signature">Signature card image</label>
                    <input type="file" name="signature" id="signature" class="w-full text-sm text-slate-700" />
                    <p class="text-xs text-slate-500 mt-1">Upload a high-quality signature card image (800x400 to 2000x1500 pixels, max 1MB) for optimal display and storage efficiency.</p>
                </div>

                <button class="inline-flex rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Save</button>
            </form>
        </div>
    </div>
@endsection