@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Member Details')
@section('page-heading', 'Member Details')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">{{ $member->name }}</h2>
                    <p class="mt-2 text-sm text-slate-500">Account number: <span class="font-medium text-slate-900">{{ $member->account_number }}</span></p>
                    <p class="mt-2 text-sm text-slate-500">Created by: <span class="font-medium text-slate-900">{{ $member->creator?->name ?? 'Unknown' }}</span></p>
                </div>

                @if (auth()->user()->canManageMember($member))
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center justify-center rounded-3xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Edit Signature Card</a>
                        <a href="{{ route('members.delete.preview', $member) }}" class="inline-flex items-center justify-center rounded-3xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">Delete Member</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="mb-4 text-sm font-semibold text-slate-700">Signature</p>
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 p-4">
                @if ($member->signature)
                    <img src="{{ route('members.signature.show', $member) }}" class="mx-auto w-full max-w-2xl rounded-2xl bg-white p-2" alt="Member signature" />
                @else
                    <p class="text-sm text-slate-500">No signature card image is attached to this member.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
