<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'SignatureSuite'))</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    @auth
        <div class="lg:flex lg:min-h-screen">
            <aside class="hidden lg:flex lg:w-72 xl:w-80 flex-col bg-slate-950 text-white shadow-xl">
                <div class="flex items-center gap-3 border-b border-white/10 px-6 py-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-emerald-500/15 text-emerald-200 font-semibold">SS</div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ config('app.name', 'SignatureSuite') }}</p>
                        <p class="mt-1 text-xl font-semibold">Member Hub</p>
                    </div>
                </div>

                <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
                    <a href="{{ route('dashboard') }}" class="block rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('members.index') }}" class="block rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('members.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        Members
                    </a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="block rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Staff
                        </a>
                        <a href="{{ route('admin.activity-logs') }}" class="block rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.activity-logs') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Activity Logs
                        </a>
                    @endif
                </nav>

                <div class="border-t border-white/10 px-6 py-5">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Signed in as</p>
                    <p class="mt-2 text-sm font-medium">{{ auth()->user()->name }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <div class="flex-1">
                <header class="bg-white border-b border-slate-200 shadow-sm">
                    <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:px-8 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ config('app.name', 'SignatureSuite') }}</p>
                            <h1 class="mt-2 text-2xl font-semibold text-slate-900">@yield('page-heading', 'Dashboard')</h1>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="hidden rounded-3xl bg-slate-100 px-4 py-3 text-sm text-slate-700 sm:block">
                                Good to see you, {{ auth()->user()->name }}
                            </div>
                        </div>
                    </div>
                </header>

                <main class="bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-7xl space-y-6">
                        @if (session('success'))
                            <div id="flash-success" class="success-toast rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900 shadow-sm flex justify-between items-start gap-3">
                                <div>
                                    <p class="font-semibold">Success</p>
                                    <p class="mt-1">{{ session('success') }}</p>
                                </div>
                                <button type="button" id="flash-close" class="text-emerald-900 hover:text-emerald-800 font-bold">×</button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    @else
        <div class="min-h-screen bg-slate-50">
            <div class="mx-auto grid w-full max-w-6xl gap-10 px-4 py-12 lg:grid-cols-[1.2fr_0.9fr] xl:px-8">
                <section class="hidden rounded-[2rem] bg-gradient-to-br from-emerald-700 via-slate-900 to-slate-950 p-10 text-white shadow-2xl lg:block">
                    <div class="max-w-xl space-y-6">
                        <span class="inline-flex rounded-full bg-white/10 px-4 py-2 text-xs uppercase tracking-[0.3em] text-emerald-100">{{ config('app.name', 'SignatureSuite') }}</span>
                        <h1 class="text-4xl font-semibold leading-tight">Secure access for staff and signature cards</h1>
                        <p class="text-sm leading-6 text-emerald-100/85">Fast, trusted login built for staff and admins. Manage members, users, and member card details with confidence.</p>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-3xl bg-white/10 p-4">
                                <p class="text-xs uppercase tracking-[0.24em] text-emerald-200">Secure</p>
                                <p class="mt-2 text-sm text-white/90">Encrypted login and password protection.</p>
                            </div>
                            <div class="rounded-3xl bg-white/10 p-4">
                                <p class="text-xs uppercase tracking-[0.24em] text-emerald-200">Reliable</p>
                                <p class="mt-2 text-sm text-white/90">Stable dashboard access for approved users.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] bg-white p-8 shadow-2xl ring-1 ring-slate-900/5">
                    @if (session('success'))
                        <div id="flash-success" class="success-toast mb-4 rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900 shadow-sm flex justify-between items-start gap-3">
                            <div>
                                <p class="font-semibold">Success</p>
                                <p class="mt-1">{{ session('success') }}</p>
                            </div>
                            <button type="button" id="flash-close" class="text-emerald-900 hover:text-emerald-800 font-bold">×</button>
                        </div>
                    @endif
                    @yield('content')
                </section>
            </div>
        </div>
    @endauth

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');

            // Auto focus on load
            if (searchInput) {
                searchInput.focus();
            }

            // Press "/" to focus search
            document.addEventListener('keydown', function(e) {
                if (e.key === '/' && document.activeElement !== searchInput) {
                    e.preventDefault();
                    searchInput.focus();
                }
            });

            // Optional: ESC clears input
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && searchInput) {
                    searchInput.value = '';
                }
            });

            const flash = document.getElementById('flash-success');
            const flashClose = document.getElementById('flash-close');

            if (flash) {
                const hideFlash = () => flash.remove();
                const timer = setTimeout(hideFlash, 7000);

                if (flashClose) {
                    flashClose.addEventListener('click', () => {
                        clearTimeout(timer);
                        hideFlash();
                    });
                }
            }

            const forms = document.querySelectorAll('form');

            forms.forEach((form) => {
                const method = (form.method || 'get').toLowerCase();

                if (method === 'get') {
                    return;
                }

                form.addEventListener('submit', () => {
                    const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');

                    submitButtons.forEach((button) => {
                        if (button.disabled) {
                            return;
                        }

                        button.disabled = true;
                        button.classList.add('cursor-not-allowed', 'opacity-70');

                        if (button.tagName.toLowerCase() === 'button') {
                            if (!button.dataset.originalContent) {
                                button.dataset.originalContent = button.innerHTML;
                            }

                            button.innerHTML = '<span class="animate-spin mr-2 inline-block h-4 w-4 rounded-full border-2 border-current border-t-transparent"></span>Processing...';
                        } else {
                            if (!button.dataset.originalValue) {
                                button.dataset.originalValue = button.value;
                            }

                            button.value = 'Processing...';
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>
