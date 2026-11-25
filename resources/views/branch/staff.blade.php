@extends('layouts.portal')
@section('title', 'Branch staff')
@section('heading', 'Staff — '.$branch->name)
@section('content')
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Designation</th><th class="px-5 py-3">Contact</th><th class="px-5 py-3">Status</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($staff as $member)
                <tr>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->avatarUrl() }}" class="h-9 w-9 rounded-full" alt="">
                            <span class="font-semibold text-slate-800">{{ $member->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-slate-600">{{ $member->roleLabel() }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $member->designation ?: '—' }}</td>
                    <td class="px-5 py-3 text-slate-600">
                        <p>{{ $member->email }}</p>
                        <p class="text-xs text-slate-500">{{ $member->phone }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $member->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">No staff assigned to this branch.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
