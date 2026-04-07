<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 Page Expired — {{ config('app.name', 'SignatureSuite') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <div class="mx-auto flex min-h-screen max-w-6xl flex-col items-center justify-center px-6 py-12 text-center">
        <div class="rounded-[2rem] border border-white/10 bg-slate-900/90 p-10 shadow-2xl backdrop-blur-sm">
            <p class="text-sm uppercase tracking-[0.35em] text-emerald-300">Page Expired</p>
            <h1 class="mt-6 text-6xl font-black tracking-tight text-white">419</h1>
            <p class="mt-6 text-xl font-semibold text-slate-200">Session expired.</p>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-400">Your session has ended or the page has been idle too long. Refresh and try again.</p>
            <div class="mt-8 flex justify-center gap-3">
                <a href="{{ url()->current() }}" class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-400">Refresh Page</a>
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-full border border-white/10 bg-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/15">Home</a>
            </div>
        </div>
    </div>
</body>
</html>
