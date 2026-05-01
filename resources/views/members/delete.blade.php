@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Delete Member')
@section('page-heading', 'Delete Member')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
        <div class="space-y-6">
            <div class="rounded-3xl border border-red-200 bg-red-50 p-6 text-red-800 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.2em]">Confirm deletion</p>
                <h2 class="mt-3 text-2xl font-semibold">Review this member carefully before deleting.</h2>
                <p class="mt-2 text-sm leading-6">This action permanently removes the member record and its saved signature card image.</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <dl class="grid gap-5 md:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">Account Number</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $member->account_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">Member Name</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $member->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">Created By</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $member->creator?->name ?? 'Unknown' }}</dd>
                    </div>
                </dl>

                <form method="POST" action="{{ route('members.destroy', $member) }}" class="mt-8 space-y-5">
                    @csrf
                    @method('DELETE')

                    <div>
                        <label for="current_password" class="mb-2 block text-sm font-medium text-slate-700">Confirm Your Password</label>
                        <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                        <p class="mt-1 text-xs text-slate-500">This extra check helps prevent accidental or unauthorized deletion from an unlocked session.</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="inline-flex rounded-3xl bg-red-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-red-700">Delete Member</button>
                        <a href="{{ route('members.show', $member) }}" class="inline-flex rounded-3xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Preview</p>
            <h2 class="mt-2 text-lg font-semibold text-slate-900">Member card to be removed</h2>
            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 p-3">
                @if ($member->signature)
                    <img src="{{ route('members.signature.show', $member) }}" alt="Member signature preview" class="w-full rounded-xl bg-white p-2" />
                @else
                    <p class="text-sm text-slate-500">No signature image found for this member.</p>
                @endif
            </div>
        </aside>
    </div>
@endsection
