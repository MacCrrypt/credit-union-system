@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Edit Member')
@section('page-heading', 'Edit Member Signature Card')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
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

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700" for="account_number">Account Number</label>
                        <input id="account_number" type="text" name="account_number" value="{{ old('account_number', $member->account_number) }}" readonly class="w-full cursor-not-allowed rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-500 shadow-sm" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700" for="name">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $member->name) }}" readonly class="w-full cursor-not-allowed rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-500 shadow-sm" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700" for="signature">Signature Card Image</label>
                        <input type="file" name="signature" id="signature" accept="image/*" class="w-full text-sm text-slate-700" />
                        <p class="mt-1 text-xs text-slate-500">Account number stays locked. Upload a new signature image only when you need to replace the existing card.</p>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="inline-flex rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Update Signature Card</button>
                        <a href="{{ route('members.show', $member) }}" class="inline-flex rounded-3xl bg-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-300">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Preview</p>
            <h2 class="mt-2 text-lg font-semibold text-slate-900">Verify the card before updating</h2>
            <div class="mt-6 space-y-5">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Account Number</p>
                    <p class="mt-2 text-base font-semibold text-slate-900">{{ $member->account_number }}</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Member Name</p>
                    <p class="mt-2 text-base font-semibold text-slate-900">{{ $member->name }}</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Signature Card</p>
                    <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200 bg-white p-3">
                        @if ($member->signature)
                            <img id="signature-preview-image" src="{{ route('members.signature.show', $member) }}" alt="Current signature" class="w-full rounded-xl" />
                            <p id="signature-preview-empty" class="hidden text-sm text-slate-500">Choose a new signature image to preview it here.</p>
                        @else
                            <img id="signature-preview-image" src="" alt="Signature preview" class="hidden w-full rounded-xl" />
                            <p id="signature-preview-empty" class="text-sm text-slate-500">Choose a new signature image to preview it here.</p>
                        @endif
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const signatureInput = document.getElementById('signature');
            const previewImage = document.getElementById('signature-preview-image');
            const previewEmpty = document.getElementById('signature-preview-empty');
            const originalSrc = previewImage.getAttribute('src');

            signatureInput.addEventListener('change', function () {
                const [file] = signatureInput.files;

                if (!file) {
                    // Revert to the stored card when no replacement file is selected.
                    if (originalSrc) {
                        previewImage.src = originalSrc;
                        previewImage.classList.remove('hidden');
                        previewEmpty.classList.add('hidden');
                    } else {
                        previewImage.src = '';
                        previewImage.classList.add('hidden');
                        previewEmpty.classList.remove('hidden');
                    }

                    return;
                }

                previewImage.src = URL.createObjectURL(file);
                previewImage.classList.remove('hidden');
                previewEmpty.classList.add('hidden');
            });
        });
    </script>
@endsection
