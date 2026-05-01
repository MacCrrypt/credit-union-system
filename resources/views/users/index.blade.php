@extends('layouts.app')

@section('title', config('app.name', 'SignatureSuite') . ' - ' . $heading)
@section('page-heading', $heading)

@section('content')
    <div class="space-y-6">
        @if (! empty($summaryStats))
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($summaryStats as $stat)
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $stat['label'] }}</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">{{ $heading }}</h2>
                <p class="mt-1 text-sm text-slate-500">Manage access, ownership, and accountability for users in the system.</p>
            </div>

            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                {{ $buttonLabel }}
            </a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-sm uppercase tracking-[0.16em] text-slate-500">
                        <tr>
                            <th class="p-4">Name</th>
                            <th class="p-4">Email</th>
                            @if ($showBranch)
                                <th class="p-4">Branch</th>
                            @endif
                            <th class="p-4">Role</th>
                            @if ($showCreator)
                                <th class="p-4">Created By</th>
                            @endif
                            <th class="p-4">Status</th>
                            <th class="p-4">Last Login</th>
                            @if ($showUserCounts)
                                <th class="p-4">Users Created</th>
                            @endif
                            @if ($showMemberCounts)
                                <th class="p-4">Members Created</th>
                            @endif
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-t align-top hover:bg-slate-50">
                                <td class="p-4 font-medium text-slate-900">{{ $user->name }}</td>
                                <td class="p-4 text-slate-700">{{ $user->email }}</td>
                                @if ($showBranch)
                                    <td class="p-4 text-slate-700">{{ $user->branch->name ?? '-' }}</td>
                                @endif
                                <td class="p-4 text-slate-700">{{ str_replace('_', ' ', $user->role) }}</td>
                                @if ($showCreator)
                                    <td class="p-4 text-slate-700">{{ $user->creator?->name ?? 'System' }}</td>
                                @endif
                                <td class="p-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $user->isActive() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($user->status ?? 'active') }}
                                    </span>
                                    @if ($user->mustChangePassword())
                                        <div class="mt-2 text-xs text-amber-600">Password change required</div>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-700">
                                    {{ $user->last_login_at?->format('M d, Y H:i') ?? 'Never' }}
                                </td>
                                @if ($showUserCounts)
                                    <td class="p-4 text-slate-700">{{ $user->role === 'admin' ? $user->created_users_count : '-' }}</td>
                                @endif
                                @if ($showMemberCounts)
                                    <td class="p-4 text-slate-700">{{ $user->role === 'staff' ? $user->members_created_count : '-' }}</td>
                                @endif
                                <td class="p-4">
                                    {{-- Actions are permission-aware so the table can be reused by both admin levels safely. --}}
                                    <div class="flex flex-wrap justify-end gap-2">
                                        @if (auth()->user()->canResetPasswordFor($user))
                                            <a href="{{ route('admin.users.password.edit', $user) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-emerald-500 hover:text-emerald-600">
                                                Reset Password
                                            </a>
                                        @endif

                                        @if (auth()->user()->canDisableUser($user))
                                            <a href="{{ route('admin.users.status.edit', $user) }}" class="inline-flex items-center justify-center rounded-2xl border border-amber-300 px-4 py-2 text-sm font-medium text-amber-700 transition hover:border-amber-400 hover:bg-amber-50">
                                                Disable
                                            </a>
                                        @endif

                                        @if (auth()->user()->canEnableUser($user))
                                            <a href="{{ route('admin.users.status.edit', $user) }}" class="inline-flex items-center justify-center rounded-2xl border border-emerald-300 px-4 py-2 text-sm font-medium text-emerald-700 transition hover:border-emerald-400 hover:bg-emerald-50">
                                                Reactivate
                                            </a>
                                        @endif

                                        @if (! auth()->user()->canResetPasswordFor($user) && ! auth()->user()->canDisableUser($user) && ! auth()->user()->canEnableUser($user))
                                            <span class="text-sm text-slate-400">No actions available</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t">
                                <td colspan="{{ 6 + ($showBranch ? 1 : 0) + ($showCreator ? 1 : 0) + ($showUserCounts ? 1 : 0) + ($showMemberCounts ? 1 : 0) }}" class="p-6 text-center text-sm text-slate-500">
                                    No users found yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
