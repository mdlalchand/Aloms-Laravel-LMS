@extends('layouts.portal')
@section('title', 'Branch students')
@section('heading', 'Students — '.$branch->name)
@section('content')
<div class="mb-4 flex flex-wrap items-center gap-3">
    <form method="GET" class="flex max-w-md flex-1 gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="Search name, email or phone" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Search</button>
    </form>
    <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-bold text-emerald-700">{{ $students->total() }} students</span>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr><th class="px-5 py-3">Student</th><th class="px-5 py-3">Contact</th><th class="px-5 py-3">Courses</th><th class="px-5 py-3">Orders</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"></th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($students as $student)
                <tr>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $student->avatarUrl() }}" class="h-9 w-9 rounded-full" alt="">
                            <p class="font-semibold text-slate-800">{{ $student->name }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-slate-600">
                        <p>{{ $student->email }}</p>
                        <p class="text-xs text-slate-500">{{ $student->phone }}</p>
                    </td>
                    <td class="px-5 py-3">{{ $student->enrollments_count }}</td>
                    <td class="px-5 py-3">{{ $student->orders_count }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $student->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $student->is_active ? 'Active' : 'Inactive' }}</span>
                    </td>
                    <td class="px-5 py-3 text-right"><a href="{{ route('branch.students.show', $student) }}" class="text-sm font-bold text-emerald-700">View →</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No students found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $students->links() }}</div>
@endsection
