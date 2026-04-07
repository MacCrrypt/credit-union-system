@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' — Member Details')
@section('page-heading', 'Member Details')

@section('content')
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm max-w-4xl">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">{{ $member->name }}</h2>
                    <p class="mt-2 text-sm text-slate-500">Account number: <span class="font-medium text-slate-900">{{ $member->account_number }}</span></p>
                </div>
                <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center justify-center rounded-3xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Edit Signature Card</a>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
            <p class="mb-4 text-sm font-semibold text-slate-700">Signature</p>
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-4">
                <img src="{{ asset('storage/' . $member->signature->image_path) }}" class="w-full max-w-2xl mx-auto" alt="Member signature" />
            </div>
        </div>
    </div>
@endsection