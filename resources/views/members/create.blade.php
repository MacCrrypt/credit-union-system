@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - Add Member')
@section('page-heading', 'Add Member')

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
                <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label for="account_number" class="mb-2 block text-sm font-medium text-slate-700">Account Number</label>
                        <input id="account_number" type="text" name="account_number" value="{{ old('account_number') }}" placeholder="Account Number" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Member Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Name" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700" for="signature">Signature card image</label>
                        <input type="file" name="signature" id="signature" accept="image/*" class="w-full text-sm text-slate-700" />
                        <p class="mt-1 text-xs text-slate-500">Upload a high-quality signature card image (800x400 to 2000x1500 pixels, max 1MB).</p>
                    </div>

                    <button class="inline-flex rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Save Member</button>
                </form>
            </div>
        </div>

        <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Preview</p>
            <h2 class="mt-2 text-lg font-semibold text-slate-900">Check before saving</h2>
            <div class="mt-6 space-y-5">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Account Number</p>
                    <p id="preview-account-number" class="mt-2 text-base font-semibold text-slate-900">Not entered yet</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Member Name</p>
                    <p id="preview-member-name" class="mt-2 text-base font-semibold text-slate-900">Not entered yet</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Signature Card</p>
                    <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200 bg-white p-3">
                        <img id="signature-preview-image" src="" alt="Signature preview" class="hidden w-full rounded-xl" />
                        <p id="signature-preview-empty" class="text-sm text-slate-500">Choose a signature image to preview it here.</p>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const accountInput = document.getElementById('account_number');
            const nameInput = document.getElementById('name');
            const signatureInput = document.getElementById('signature');
            const previewAccount = document.getElementById('preview-account-number');
            const previewName = document.getElementById('preview-member-name');
            const previewImage = document.getElementById('signature-preview-image');
            const previewEmpty = document.getElementById('signature-preview-empty');

            // The live preview reduces wrong-account and wrong-image saves before
            // the record ever reaches the database.
            const syncText = () => {
                previewAccount.textContent = accountInput.value.trim() || 'Not entered yet';
                previewName.textContent = nameInput.value.trim() || 'Not entered yet';
            };

            const syncImage = () => {
                const [file] = signatureInput.files;

                if (!file) {
                    previewImage.src = '';
                    previewImage.classList.add('hidden');
                    previewEmpty.classList.remove('hidden');
                    return;
                }

                previewImage.src = URL.createObjectURL(file);
                previewImage.classList.remove('hidden');
                previewEmpty.classList.add('hidden');
            };

            accountInput.addEventListener('input', syncText);
            nameInput.addEventListener('input', syncText);
            signatureInput.addEventListener('change', syncImage);

            syncText();
            syncImage();
        });
    </script>
@endsection
