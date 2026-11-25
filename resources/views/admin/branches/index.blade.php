@extends('layouts.admin')
@section('title', 'Branches')
@section('heading', 'Branches')
@section('actions')
    <a href="{{ route('admin.branches.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ New branch</a>
@endsection
@section('content')
<div class="mb-4 flex flex-wrap items-center gap-3">
    <div class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm">
        <span class="text-slate-500">Branches:</span> <b>{{ $branches->total() }}</b>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm">
        <span class="text-slate-500">Total revenue (all):</span> <b>{{ \App\Models\Setting::get('currency', '৳') }}{{ number_format($totalRevenue) }}</b>
    </div>
    <form method="GET" class="ml-auto flex gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="Search name / code" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Search</button>
    </form>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-5 py-3">Branch</th>
                <th class="px-5 py-3">Manager</th>
                <th class="px-5 py-3">Students</th>
                <th class="px-5 py-3">All users</th>
                <th class="px-5 py-3">Revenue</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($branches as $branch)
                <tr>
                    <td class="px-5 py-3">
                        <p class="font-semibold text-slate-800">{{ $branch->name }}</p>
                        <p class="text-xs text-slate-500">{{ $branch->code }} · {{ $branch->phone ?: 'no phone' }}</p>
                    </td>
                    <td class="px-5 py-3 text-slate-600">{{ $branch->manager?->name ?? '—' }}</td>
                    <td class="px-5 py-3 font-semibold">{{ $branch->students_count }}</td>
                    <td class="px-5 py-3">{{ $branch->users_count }}</td>
                    <td class="px-5 py-3">{{ \App\Models\Setting::get('currency', '৳') }}{{ number_format($branch->revenue()) }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $branch->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $branch->is_active ? 'Active' : 'Closed' }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-5 py-3 text-right">
                        <a href="{{ route('admin.users.index', ['branch' => $branch->id]) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">People</a>
                        <form method="POST" action="{{ route('admin.branches.toggle', $branch) }}" class="ml-3 inline">
                            @csrf
                            <button type="submit" class="text-sm font-semibold text-slate-600 hover:text-slate-900">{{ $branch->is_active ? 'Close' : 'Open' }}</button>
                        </form>
                        <a href="{{ route('admin.branches.edit', $branch) }}" class="ml-3 text-sm font-semibold text-emerald-700">Edit</a>
                        <form method="POST" action="{{ route('admin.branches.destroy', $branch) }}" class="inline" onsubmit="return confirm('Remove this branch? Users will be unassigned.')">
                            @csrf @method('DELETE')
                            <button class="ml-3 text-sm font-semibold text-rose-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-slate-500">No branches yet. Create your first branch to organise students by centre.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $branches->links() }}</div>
@endsection
