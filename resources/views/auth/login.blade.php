@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' — Sign In')

@section('content')
    <div class="flex h-full flex-col justify-center gap-6">
        <div class="space-y-3 text-center">
            <p class="inline-flex rounded-full bg-emerald-50 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">Secure Access</p>
            <h1 class="text-3xl font-semibold text-slate-900 sm:text-4xl">Sign in to SignatureSuite</h1>
            <p class="mx-auto max-w-md text-sm leading-6 text-slate-500">Access your member and signature card dashboard with secure sign in for staff and administrators.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div class="rounded-3xl bg-slate-50 p-6 shadow-sm ring-1 ring-slate-200">
                <div class="space-y-5">
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-gray-300 bg-white px-4 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="mt-2 block w-full rounded-2xl border-gray-300 bg-white px-4 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                        type="password"
                                        name="password"
                                        required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
                            <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" name="remember">
                            <span>{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-medium text-emerald-600 hover:text-emerald-700" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid gap-4">
                <x-primary-button class="w-full py-3 text-base">
                    {{ __('Log in') }}
                </x-primary-button>
                <p class="text-center text-sm text-slate-500">Need help? Contact your admin for access support.</p>
            </div>
        </form>
    </div>
@endsection