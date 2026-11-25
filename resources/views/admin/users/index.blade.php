@extends('layouts.admin')
@section('title', 'Users')
@section('heading', 'Students & staff')
@section('actions')
    @if (auth()->user()->isAdmin())
        <a href="{{ route('admin.users.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ New user</a>
    @endif
@endsection
@section('content')
<div class="mb-4 flex flex-wrap items-center gap-2">
    @foreach (['' => 'All', 'student' => 'Students', 'instructor' => 'Teachers', 'guardian' => 'Guardians', 'branch_manager' => 'Branch managers', 'admin' => 'Admins'] as $value => $label)
        <a href="{{ route('admin.users.index', array_filter(['role' => $value, 'q' => request('q')])) }}"
           class="rounded-lg px-3 py-1.5 text-sm font-semibold {{ request('role', '') === $value ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200' }}">
            {{ $label }} ({{ $counts[$value ?: 'all'] }})
        </a>
    @endforeach
    <form method="GET" class="ml-auto flex gap-2">
        <input type="hidden" name="role" value="{{ request('role') }}">
        <input name="q" value="{{ request('q') }}" placeholder="Search name / email / phone" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Search</button>
    </form>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Courses</th><th class="px-5 py-3">Enrollments</th><th class="px-5 py-3">Orders</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($users as $user)
                <tr>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->avatarUrl() }}" class="h-9 w-9 rounded-full" alt="">
                            <div>
                                <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-slate-800 hover:text-emerald-700">{{ $user->name }}</a>
                                <p class="text-xs text-slate-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 capitalize text-slate-600">
                        {{ $user->roleLabel() }}
                        @if ($user->branch)
                            <p class="text-xs text-slate-500">🏢 {{ $user->branch->name }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3">{{ $user->courses_count }}</td>
                    <td class="px-5 py-3">{{ $user->enrollments_count }}</td>
                    <td class="px-5 py-3">{{ $user->orders_count }}</td>
                    <td class="px-5 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $user->is_active ? 'Active' : 'Disabled' }}</span></td>
                    <td class="px-5 py-3 text-right"><a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-semibold text-emerald-700">Edit</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
