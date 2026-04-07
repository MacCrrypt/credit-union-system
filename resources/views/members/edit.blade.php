@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' — Edit Member')
@section('page-heading', 'Edit Member Signature Card')

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
            <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700" for="account_number">Account Number</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $member->account_number) }}" readonly class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-500 shadow-sm cursor-not-allowed" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700" for="name">Name</label>
                    <input type="text" name="name" value="{{ old('name', $member->name) }}" readonly class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-500 shadow-sm cursor-not-allowed" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700" for="signature">Signature Card Image</label>
                    @if($member->signature)
                        <div class="mb-4">
                            <p class="text-sm text-slate-600 mb-2">Current signature:</p>
                            <img src="{{ asset('storage/' . $member->signature->image_path) }}" alt="Current Signature" class="max-w-xs border border-slate-200 rounded-lg shadow-sm" />
                        </div>
                    @endif
                    <input type="file" name="signature" id="signature" class="w-full text-sm text-slate-700" />
                    <p class="text-xs text-slate-500 mt-1">Upload a new signature image (800x400 to 2000x1500 pixels, max 1MB) to replace the current one. Leave empty to keep the current signature.</p>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="inline-flex rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Update Signature Card</button>
                    <a href="{{ route('members.index') }}" class="inline-flex rounded-3xl bg-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-300">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection